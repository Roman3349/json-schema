<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft2019;

class AdditionalPropertiesConstraint extends \JsonSchema\Constraints\Drafts\Draft07\AdditionalPropertiesConstraint
{
    public function __construct(?Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
