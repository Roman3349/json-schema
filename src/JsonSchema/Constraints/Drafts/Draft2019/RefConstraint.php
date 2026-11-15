<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft2019;

class RefConstraint extends \JsonSchema\Constraints\Drafts\Draft07\RefConstraint
{
    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
