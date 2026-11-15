<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\Constraints\ConstraintInterface;
use JsonSchema\Constraints\Drafts;
use JsonSchema\Tests\Constraints\VeryBaseTestCase;

class AdditionalPropertiesConstraintTest extends VeryBaseTestCase
{
    /**
     * @dataProvider provideAdditionalPropertiesConstraints
     *
     * @param class-string<ConstraintInterface> $constraintClass
     */
    public function testSubSchemaErrorsAreReportedAtThePropertyPath(string $constraintClass): void
    {
        $constraint = $this->createConstraint($constraintClass);
        $schema = json_decode('{"properties": {"foo": {}}, "additionalProperties": {"type": "integer"}}');
        $value = json_decode('{"foo": "ok", "bar": "not-an-integer"}');

        $constraint->check($value, $schema);

        $errors = $constraint->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('/bar', $errors[0]['pointer']);
        $this->assertSame('type', $errors[0]['constraint']['name']);
    }

    /**
     * @dataProvider provideAdditionalPropertiesConstraints
     *
     * @param class-string<ConstraintInterface> $constraintClass
     */
    public function testAPropertyMatchingTheSubSchemaIsNotReported(string $constraintClass): void
    {
        $constraint = $this->createConstraint($constraintClass);
        $schema = json_decode('{"additionalProperties": {"type": "integer"}}');
        $value = json_decode('{"bar": 1}');

        $constraint->check($value, $schema);

        $this->assertSame([], $constraint->getErrors());
    }

    /**
     * @dataProvider provideAdditionalPropertiesConstraints
     *
     * @param class-string<ConstraintInterface> $constraintClass
     */
    public function testAdditionalPropertiesFalseStillReportsTheProperty(string $constraintClass): void
    {
        $constraint = $this->createConstraint($constraintClass);
        $schema = json_decode('{"properties": {"foo": {}}, "additionalProperties": false}');
        $value = json_decode('{"foo": "ok", "bar": 1}');

        $constraint->check($value, $schema);

        $errors = $constraint->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('/bar', $errors[0]['pointer']);
        $this->assertSame('additionalProp', $errors[0]['constraint']['name']);
    }

    /**
     * @return array<string, array{class-string<ConstraintInterface>}>
     */
    public static function provideAdditionalPropertiesConstraints(): array
    {
        return [
            'Draft-06' => [Drafts\Draft06\AdditionalPropertiesConstraint::class],
            'Draft-07' => [Drafts\Draft07\AdditionalPropertiesConstraint::class],
            'Draft 2019-09' => [Drafts\Draft2019\AdditionalPropertiesConstraint::class],
            'Draft 2020-12' => [Drafts\Draft2020\AdditionalPropertiesConstraint::class],
        ];
    }

    /**
     * @param class-string<ConstraintInterface> $constraintClass
     */
    private function createConstraint(string $constraintClass): ConstraintInterface
    {
        return new $constraintClass();
    }
}
