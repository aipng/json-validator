<?php

declare(strict_types = 1);

namespace AipNg\JsonValidator;

use AipNg\JsonValidator\Exception\InvalidJsonDataException;
use AipNg\JsonValidator\Exception\UnresolvedSchemaException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Exceptions\UnresolvedReferenceException;
use Opis\JsonSchema\Parsers\SchemaParser;
use Opis\JsonSchema\Resolvers\SchemaResolver;
use Opis\JsonSchema\SchemaLoader;
use Opis\JsonSchema\Validator;

final class JsonValidator
{

	private Validator $validator;


	public function __construct(int $maxErrors = 50, ?string $prefix = null, ?string $directory = null)
	{
		$schemaResolver = new SchemaResolver;

		if ($prefix && $directory) {
			if (!is_readable($directory)) {
				throw new \InvalidArgumentException(sprintf('Directory \'%s\' not readable!', $directory));
			}

			$schemaResolver->registerPrefix($prefix, $directory);
		}

		$this->validator = new Validator(new SchemaLoader(new SchemaParser, $schemaResolver), $maxErrors);
	}


	/**
	 * @throws \AipNg\JsonValidator\Exception\InvalidJsonDataException
	 * @throws \AipNg\JsonValidator\Exception\UnresolvedSchemaException
	 */
	public function validate(string $json, string $schemaPath): void
	{
		try {
			$result = $this->validator->validate(
				Json::decode($json),
				file_get_contents($schemaPath),
			);
		} catch (JsonException $e) {
			throw new InvalidJsonDataException('Invalid JSON', 500, $e);
		} catch (UnresolvedReferenceException $e) {
			throw new UnresolvedSchemaException($e->getMessage(), 500, $e);
		}

		if (!$result->isValid()) {
			$exception = new InvalidJsonDataException;

			$validationErrors = $result->error();

			if ($validationErrors) {
				$exception->addErrors((new ErrorFormatter)->format($validationErrors, false));
			}

			throw $exception;
		}
	}

}
