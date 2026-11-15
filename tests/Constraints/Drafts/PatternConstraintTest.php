<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\Constraints\ConstraintInterface;
use JsonSchema\Constraints\Drafts;
use JsonSchema\Tests\Constraints\VeryBaseTestCase;

class PatternConstraintTest extends VeryBaseTestCase
{
    /**
     * @dataProvider providePatternConstraints
     *
     * @param class-string<ConstraintInterface> $constraintClass
     */
    public function testErrorMessageContainsThePattern(string $constraintClass): void
    {
        $constraint = new $constraintClass();
        $schema = json_decode('{"pattern": "^[a-z]+$"}');
        $value = 'ABC';

        $constraint->check($value, $schema);

        $errors = $constraint->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame('Does not match the regex pattern ^[a-z]+$', $errors[0]['message']);
        $this->assertSame(['pattern' => '^[a-z]+$', 'found' => 'ABC'], $errors[0]['constraint']['params']);
    }

    /**
     * @return array<string, array{class-string<ConstraintInterface>}>
     */
    public static function providePatternConstraints(): array
    {
        return [
            'Draft-06' => [Drafts\Draft06\PatternConstraint::class],
            'Draft-07' => [Drafts\Draft07\PatternConstraint::class],
            'Draft 2019-09' => [Drafts\Draft2019\PatternConstraint::class],
            'Draft 2020-12' => [Drafts\Draft2020\PatternConstraint::class],
        ];
    }
}
