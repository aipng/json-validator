# JSON validator

JSON validator (based on [opis/json-schema](https://github.com/opis/json-schema))

## Installation

```bash
composer require aipng/json-validator
```

## Usage

Simple - validate JSON input against simple JSON schema
```php
    use \AipNg\JsonValidator\JsonValidator;
    
    $validator = new JsonValidator;
    
    $validator->validate($jsonData, $jsonSchemaPath);
```

Validator provides simple mapping of JSON schema indentificators to a directory structure, eg:

- https://example.org/schemas/foo.json -> `/<my-project-schema-path/foo.json`
- https://example.org/schemas/bar.json -> `/<my-project-schema-path/bar.json`

```php
    use \AipNg\JsonValidator\JsonValidator;
    
    $validator = new JsonValidator(10, 'https://example.org/schemas/', '/<my-project-schema-path/');
    
    $validator->validate($jsonData, $jsonSchemaPath);
```

## Nette extension

Register
```neon
extensions:
	jsonValidator: AipNg\JsonValidator\DI\JsonValidatorExtension
```

Configure
```neon
jsonValidator:
	max_errors: 10              # maximum of returned errors
	mapping:                    # maps JSON schema identificators to directory structure
		prefix: https://www.example.org/api/schema/
		directory: %wwwDir%/../src/schema/
```

