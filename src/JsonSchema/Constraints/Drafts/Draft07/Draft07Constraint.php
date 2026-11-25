<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft07;

use JsonSchema\ConstraintError;
use JsonSchema\Constraints\Constraint;
use JsonSchema\DraftIdentifiers;
use JsonSchema\Entity\JsonPointer;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;

class Draft07Constraint extends Constraint
{
    /** @var bool PROTOTYPE: stop a branch at its first error */
    public static $failFast = false;

    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct(new Factory(
            $factory ? $factory->getSchemaStorage() : new SchemaStorage(),
            $factory ? $factory->getUriRetriever() : new UriRetriever(),
            $factory ? $factory->getConfig() : Constraint::CHECK_MODE_NORMAL
        ));
    }

    public function check(&$value, $schema = null, ?JsonPointer $path = null, $i = null): void
    {
        if (is_bool($schema)) {
            if ($schema === false) {
                $this->addError(ConstraintError::FALSE(), $path, []);
            }

            return;
        }

        if ($this->factory->getConfig(Constraint::CHECK_MODE_VALIDATE_SCHEMA)) {
            // validate the schema only once, not each of its subschemas
            $this->factory->removeConfig(Constraint::CHECK_MODE_VALIDATE_SCHEMA);
            try {
                $this->validateSchema($schema, $path);
                $this->check($value, $schema, $path, $i);
            } finally {
                $this->factory->addConfig(Constraint::CHECK_MODE_VALIDATE_SCHEMA);
            }

            return;
        }

        // Apply defaults
        $this->checkForKeyword('ref', $value, $schema, $path, $i);
        if (property_exists($schema, '$ref')) {
            // All other keywords in a schema containing $ref are ignored
            return;
        }

        $keywords = [];
        foreach (get_object_vars($schema) as $property => $_) {
            if (isset(self::DISPATCH[$property])) {
                $keywords[self::DISPATCH[$property][0]] = self::DISPATCH[$property][1];
            }
        }
        ksort($keywords);
        foreach ($keywords as $keyword) {
            if (self::$failFast && $this->getErrors() !== []) {
                return;
            }
            $validator = $this->factory->createInstanceFor($keyword);
            $validator->check($value, $schema, $path, $i);
            $this->addErrors($validator->getErrors());
        }
    }

    /** PROTOTYPE: schema property => [position, keyword] */
    private const DISPATCH = [
        'required' => [0, 'required'],
        'contains' => [1, 'contains'],
        'properties' => [2, 'properties'],
        'propertyNames' => [3, 'propertyNames'],
        'patternProperties' => [4, 'patternProperties'],
        'type' => [5, 'type'],
        'not' => [6, 'not'],
        'dependencies' => [7, 'dependencies'],
        'allOf' => [8, 'allOf'],
        'anyOf' => [9, 'anyOf'],
        'oneOf' => [10, 'oneOf'],
        'if' => [11, 'ifThenElse'],
        'additionalProperties' => [12, 'additionalProperties'],
        'items' => [13, 'items'],
        'additionalItems' => [14, 'additionalItems'],
        'uniqueItems' => [15, 'uniqueItems'],
        'minItems' => [16, 'minItems'],
        'minProperties' => [17, 'minProperties'],
        'maxProperties' => [18, 'maxProperties'],
        'minimum' => [19, 'minimum'],
        'maximum' => [20, 'maximum'],
        'minLength' => [21, 'minLength'],
        'exclusiveMinimum' => [22, 'exclusiveMinimum'],
        'maxItems' => [23, 'maxItems'],
        'maxLength' => [24, 'maxLength'],
        'exclusiveMaximum' => [25, 'exclusiveMaximum'],
        'enum' => [26, 'enum'],
        'const' => [27, 'const'],
        'multipleOf' => [28, 'multipleOf'],
        'format' => [29, 'format'],
        'pattern' => [30, 'pattern'],
        'contentMediaType' => [31, 'content'],
        'contentEncoding' => [31, 'content'],
    ];

    private const KEYWORD_SCHEMA_PROPERTIES = [
        'ref' => ['$ref'],
        'ifThenElse' => ['if'],
        'content' => ['contentMediaType', 'contentEncoding'],
    ];

    /**
     * Validates the schema against the meta-schema of this draft
     *
     * @param mixed $schema
     */
    private function validateSchema($schema, ?JsonPointer $path): void
    {
        $initialConfig = $this->factory->getConfig();
        // the meta-schema must not modify the schema
        $this->factory->removeConfig(Constraint::CHECK_MODE_APPLY_DEFAULTS);
        $this->factory->addConfig(Constraint::CHECK_MODE_TYPE_CAST);
        $validator = new self($this->factory);
        $this->factory->setConfig($initialConfig);

        $metaSchema = $this->factory->getSchemaStorage()->getSchema(DraftIdentifiers::DRAFT_7);
        try {
            $validator->check($schema, $metaSchema);
        } catch (\Exception $e) {
            if ($this->factory->getConfig(Constraint::CHECK_MODE_EXCEPTIONS)) {
                throw new InvalidSchemaException('Schema did not pass validation', 0, $e);
            }
        }

        $errors = $validator->getErrors();
        if ($errors === []) {
            return;
        }

        foreach ($errors as &$error) {
            $error['context'] = Validator::ERROR_SCHEMA_VALIDATION;
        }
        unset($error);
        $this->addErrors($errors);

        $initialContext = $this->factory->getErrorContext();
        $this->factory->setErrorContext(Validator::ERROR_SCHEMA_VALIDATION);
        $this->addError(ConstraintError::INVALID_SCHEMA(), $path);
        $this->factory->setErrorContext($initialContext);
    }

    /**
     * @param mixed $value
     * @param mixed $schema
     * @param mixed $i
     */
    protected function checkForKeyword(string $keyword, $value, $schema = null, ?JsonPointer $path = null, $i = null): void
    {
        if (self::$failFast && $this->getErrors() !== []) {
            return;
        }
        foreach (self::KEYWORD_SCHEMA_PROPERTIES[$keyword] ?? [$keyword] as $property) {
            if (property_exists($schema, $property)) {
                $validator = $this->factory->createInstanceFor($keyword);
                $validator->check($value, $schema, $path, $i);

                $this->addErrors($validator->getErrors());

                return;
            }
        }
    }
}
