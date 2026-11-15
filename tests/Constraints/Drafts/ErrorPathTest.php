<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\DraftIdentifiers;
use JsonSchema\Tests\Constraints\VeryBaseTestCase;
use JsonSchema\Validator;

class ErrorPathTest extends VeryBaseTestCase
{
    use StrictDialectsProvider;

    /**
     * @dataProvider provideStrictDialects
     */
    public function testNestedPropertyErrorsAreReportedAtTheirPath(string $dialect): void
    {
        $errors = $this->validate(
            $dialect,
            '{"type": "object", "required": ["bar"], "properties": {"foo": {"type": "integer"}, "n": {"type": "object", "properties": {"x": {"type": "integer"}}}}}',
            '{"foo": "bar", "n": {"x": "y"}}'
        );

        $this->assertSame(['/bar', '/foo', '/n/x'], array_column($errors, 'pointer'));
        $this->assertSame(['bar', 'foo', 'n.x'], array_column($errors, 'property'));
    }

    /**
     * @dataProvider provideStrictDialects
     */
    public function testItemErrorsAreReportedAtTheirIndex(string $dialect): void
    {
        // Draft 2020-12 replaced the array form of items and additionalItems with prefixItems and items
        $tuple = $dialect === DraftIdentifiers::DRAFT_2020_12
            ? '{"prefixItems": [{"type": "integer"}], "items": {"type": "integer"}}'
            : '{"items": [{"type": "integer"}], "additionalItems": {"type": "integer"}}';

        $errors = $this->validate(
            $dialect,
            '{"properties": {"list": {"items": {"type": "integer"}}, "tuple": ' . $tuple . '}}',
            '{"list": [1, "a"], "tuple": ["b", "c"]}'
        );

        $this->assertSame(['/list/1', '/tuple/0', '/tuple/1'], array_column($errors, 'pointer'));
    }

    /**
     * @dataProvider provideStrictDialects
     */
    public function testPatternPropertyErrorsAreReportedAtThePropertyPath(string $dialect): void
    {
        $errors = $this->validate(
            $dialect,
            '{"patternProperties": {"^p": {"type": "integer"}}}',
            '{"p1": "a", "p2": "b"}'
        );

        $this->assertSame(['/p1', '/p2'], array_column($errors, 'pointer'));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function validate(string $dialect, string $schema, string $data): array
    {
        $schema = json_decode($schema);
        $schema->{'$schema'} = $dialect;
        $data = json_decode($data);

        $validator = new Validator(new Factory(null, null, Constraint::CHECK_MODE_STRICT));
        $validator->validate($data, $schema);

        return $validator->getErrors();
    }
}
