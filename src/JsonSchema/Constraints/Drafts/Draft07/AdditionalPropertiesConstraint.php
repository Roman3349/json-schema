<?php

declare(strict_types=1);

namespace JsonSchema\Constraints\Drafts\Draft07;

class AdditionalPropertiesConstraint extends \JsonSchema\Constraints\Drafts\Draft06\AdditionalPropertiesConstraint
{
    public function __construct(?\JsonSchema\Constraints\Factory $factory = null)
    {
        parent::__construct($factory ?: new Factory());
    }
}
