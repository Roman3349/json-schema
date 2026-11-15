<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts\Draft2020;

use JsonSchema\DraftIdentifiers;

class UnevaluatedPropertiesTest extends \JsonSchema\Tests\Constraints\Drafts\Draft2019\UnevaluatedPropertiesTest
{
    protected $schemaSpec = DraftIdentifiers::DRAFT_2020_12;
}
