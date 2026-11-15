<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft06;

class OneOfConstraint extends \JsonSchema\Constraints\Drafts\Draft04\OneOfConstraint
{
    public function __construct(?Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
