<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft07;

class PatternConstraint extends \JsonSchema\Constraints\Drafts\Draft06\PatternConstraint
{
    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
