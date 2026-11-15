<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft06;

class AnyOfConstraint extends \JsonSchema\Constraints\Drafts\Draft04\AnyOfConstraint
{
    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
