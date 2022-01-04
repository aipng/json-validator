<?php

declare(strict_types = 1);

namespace AipNg\JsonValidator\Exception;

final class InvalidJsonDataException extends \Exception
{

	/** @var string[] */
	private array $errors = [];


	/** @param string[] $errors */
	public function addErrors(array $errors): void
	{
		$this->errors = $errors;
	}


	/**
	 * @return string[]
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

}
