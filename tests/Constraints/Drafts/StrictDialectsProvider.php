<?php

declare(strict_types=1);

namespace JsonSchema\Tests\Constraints\Drafts;

use JsonSchema\DraftIdentifiers;

/**
 * Provides the dialects that are validated by a draft specific constraint set in strict mode.
 */
trait StrictDialectsProvider
{
    /**
     * @return array<string, array{string}>
     */
    public static function provideStrictDialects(): array
    {
        return [
            'Draft-06' => [DraftIdentifiers::DRAFT_6],
            'Draft-07' => [DraftIdentifiers::DRAFT_7],
            'Draft 2019-09' => [DraftIdentifiers::DRAFT_2019_09],
            'Draft 2020-12' => [DraftIdentifiers::DRAFT_2020_12],
        ];
    }
}
