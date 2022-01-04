<?php

declare(strict_types = 1);

namespace AipNg\JsonValidatorTests;

use AipNg\JsonValidator\Exception\InvalidJsonDataException;
use AipNg\JsonValidator\Exception\UnresolvedSchemaException;
use AipNg\JsonValidator\JsonValidator;
use PHPUnit\Framework\TestCase;

final class JsonValidatorTest extends TestCase
{

	public function testShouldThrowExceptionOnInvalidDirectory(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		new JsonValidator(50, 'https://www.example.org', __DIR__ . '/no-directory');

	}

	public function testShouldValidateEmptySchema(): void
	{
		$validator = new JsonValidator;

		$validator->validate('{}', __DIR__ . '/Fixtures/empty-schema.json');

		$this->assertTrue(true);
	}


	public function testShouldValidateSchema(): void
	{
		$validator = new JsonValidator;

		$input = '
			{
				"id": 1,
				"name": "name",
				"items": ["first", "second"]
			}
		';

		$validator->validate($input, __DIR__ . '/Fixtures/valid-schema.json');

		$this->assertTrue(true);
	}


	public function testShouldThrowExceptionOnInvalidData(): void
	{
		$validator = new JsonValidator;

		try {
			$input = '
				{
					"id": "id",
					"name": "name",
					"items": [10, 20]
				}
			';

			$validator->validate($input, __DIR__ . '/Fixtures/valid-schema.json');
		} catch (InvalidJsonDataException $e) {
			$errors = $e->getErrors();

			$this->assertCount(3, $errors);
			$this->assertSame('The data (string) must match the type: integer', $errors['/id']);
			$this->assertSame('The data (integer) must match the type: string', $errors['/items/0']);
			$this->assertSame('The data (integer) must match the type: string', $errors['/items/1']);
		}
	}


	public function testShouldReflectMaximumErrors(): void
	{
		$validator = new JsonValidator(1);

		try {
			$input = '
				{
					"id": "id",
					"name": "name",
					"items": [10, 20]
				}
			';

			$validator->validate($input, __DIR__ . '/Fixtures/valid-schema.json');
		} catch (InvalidJsonDataException $e) {
			$errors = $e->getErrors();

			$this->assertCount(1, $errors);
			$this->assertSame('The data (string) must match the type: integer', $errors['/id']);
		}
	}


	public function testShouldThrowExceptionOnMissingMapping(): void
	{
		$validator = new JsonValidator;

		$input = '
				{
					"id": "id",
					"name": "name",
					"items": [10, 20]
				}
			';

		$this->expectException(UnresolvedSchemaException::class);

		$validator->validate($input, __DIR__ . '/Fixtures/main-schema.json');
	}


	public function testShouldThrowExceptionOnInvalidDataWithCombinedSchema(): void
	{
		$validator = new JsonValidator(50, 'https://api.example.com/api/schema/', __DIR__ . '/Fixtures/');

		try {
			$input = '
				{
					"id": 1,
					"name": "name",
					"items": [{
						"title": "item title",
						"logo": 1,
						"countable": "true"
					}]
				}
			';

			$validator->validate($input, __DIR__ . '/Fixtures/main-schema.json');
		} catch (InvalidJsonDataException $e) {
			$errors = $e->getErrors();

			$this->assertCount(2, $errors);
			$this->assertSame('The data (integer) must match the type: string', $errors['/items/0/logo']);
			$this->assertSame('The data (string) must match the type: boolean', $errors['/items/0/countable']);
		}
	}


	public function testShouldValidateCombinedSchema(): void
	{
		$validator = new JsonValidator(50, 'https://api.example.com/api/schema/', __DIR__ . '/Fixtures/');

		$input = '
			{
				"id": 1,
				"name": "name",
				"items": [{
					"title": "item title",
					"logo": "logo",
					"countable": true
				}]
			}
		';

		$validator->validate($input, __DIR__ . '/Fixtures/main-schema.json');

		$this->assertTrue(true);
	}

}
