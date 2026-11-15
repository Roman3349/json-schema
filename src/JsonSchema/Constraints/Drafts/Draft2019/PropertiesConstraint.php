<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft2019;

class PropertiesConstraint extends \JsonSchema\Constraints\Drafts\Draft07\PropertiesConstraint
{
    public function __construct(?Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
