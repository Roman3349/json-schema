<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\UnresolvableJsonPointerException;
use JsonSchema\Tests\Constraints\VeryBaseTestCase;
use JsonSchema\Validator;

class RefConstraintTest extends VeryBaseTestCase
{
    use StrictDialectsProvider;

    /**
     * @dataProvider provideStrictDialects
     */
    public function testAnUnresolvableRefThrowsInsteadOfSilentlyPassing(string $dialect): void
    {
        $validator = new Validator();
        $data = new \stdClass();
        $schema = json_decode('{"$schema": "' . $dialect . '", "$ref": "#/$defs/nonExistent"}');

        $this->expectException(UnresolvableJsonPointerException::class);

        $validator->validate($data, $schema, Constraint::CHECK_MODE_STRICT);
    }

    /**
     * @dataProvider provideStrictDialects
     */
    public function testAnUnresolvableRefInsideAnApplicatorIsNotSwallowed(string $dialect): void
    {
        $validator = new Validator();
        $data = new \stdClass();
        $schema = json_decode('{"$schema": "' . $dialect . '", "anyOf": [{"$ref": "#/$defs/nonExistent"}, {}]}');

        $this->expectException(UnresolvableJsonPointerException::class);

        $validator->validate($data, $schema, Constraint::CHECK_MODE_STRICT);
    }

    /**
     * @dataProvider provideStrictDialects
     */
    public function testAResolvableRefStillValidates(string $dialect): void
    {
        $validator = new Validator();
        $data = json_decode('{"child": 1}');
        $schema = json_decode(
            '{"$schema": "' . $dialect . '", "$defs": {"num": {"type": "integer"}},'
            . ' "properties": {"child": {"$ref": "#/$defs/num"}}}'
        );

        $validator->validate($data, $schema, Constraint::CHECK_MODE_STRICT);

        self::assertTrue($validator->isValid(), (string) json_encode($validator->getErrors()));
    }
}
