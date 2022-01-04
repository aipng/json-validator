<?php

declare(strict_types = 1);

namespace AipNg\JsonValidator\DI;

use AipNg\JsonValidator\JsonValidator;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @property \stdClass $config
 */
final class JsonValidatorExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'maxErrors' => Expect::int(50),
			'mapping' => Expect::structure([
				'prefix' => Expect::string(),
				'directory' => Expect::string(),
			]),
		]);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder
			->addDefinition($this->prefix('validator'))
			->setFactory(JsonValidator::class)
			->setArguments([
				$this->config->maxErrors,
				$this->config->mapping->prefix,
				$this->config->mapping->directory,
			]);
	}

}
