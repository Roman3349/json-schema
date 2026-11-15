<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft06;

class TypeConstraint extends \JsonSchema\Constraints\Drafts\Draft04\TypeConstraint
{
    /**
     * As of Draft-06, a float with zero fractional part is an integer.
     *
     * @param int|float $value
     */
    protected function isInteger($value): bool
    {
        return fmod($value, 1.0) === 0.0;
    }
}
