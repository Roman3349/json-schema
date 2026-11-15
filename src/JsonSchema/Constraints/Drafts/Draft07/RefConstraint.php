<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft07;

class RefConstraint extends \JsonSchema\Constraints\Drafts\Draft06\RefConstraint
{
    public function __construct(?Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
