<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft06;

class AllOfConstraint extends \JsonSchema\Constraints\Drafts\Draft04\AllOfConstraint
{
    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
