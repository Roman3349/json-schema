<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Tests\Constraints\VeryBaseTestCase;
use JsonSchema\Validator;

/**
 * ECMA-262 defines \d, \D, \w and \W over ASCII only, while PCRE makes them Unicode
 * aware under the /u modifier. Patterns must keep their ECMA meaning.
 */
class EcmaPatternTest extends VeryBaseTestCase
{
    /**
     * @dataProvider provideNonAsciiCases
     */
    public function testShorthandClassesKeepTheirAsciiOnlyEcmaMeaning(string $dialect, string $schema, string $data, bool $expectedToBeValid): void
    {
        $validator = new Validator();
        $decodedSchema = json_decode($schema);
        $decodedSchema->{'$schema'} = $dialect;
        $decodedData = json_decode($data);
        $validator->validate($decodedData, $decodedSchema, Constraint::CHECK_MODE_STRICT);

        self::assertSame($expectedToBeValid, $validator->isValid(), (string) json_encode($validator->getErrors()));
    }

    /**
     * @return \Generator<string, array{string, string, string, bool}>
     */
    public static function provideNonAsciiCases(): \Generator
    {
        $cases = [
            'ASCII digit matches \\d in patternProperties' => [
                '{"patternProperties": {"^\\\\d+$": {"type": "string"}}, "additionalProperties": false}',
                '{"5": "ok"}',
                true,
            ],
            'Arabic-Indic digit does not match \\d in patternProperties' => [
                '{"patternProperties": {"^\\\\d+$": {"type": "string"}}, "additionalProperties": false}',
                '{"٣": "ok"}',
                false,
            ],
            'accented letter does not match \\w in pattern' => [
                '{"type": "string", "pattern": "^\\\\w+$"}',
                '"é"',
                false,
            ],
            '\\w inside a character class in patternProperties' => [
                '{"patternProperties": {"^[\\\\w-]+$": {"type": "string"}}, "additionalProperties": false}',
                '{"foo-bar": "ok"}',
                true,
            ],
        ];

        foreach (StrictDialectsProvider::provideStrictDialects() as $dialectName => [$dialect]) {
            foreach ($cases as $caseName => [$schema, $data, $expectedToBeValid]) {
                yield $dialectName . ': ' . $caseName => [$dialect, $schema, $data, $expectedToBeValid];
            }
        }
    }
}
