<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\I18n;

use Magento\Setup\Module\I18n\Locale;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    /**
     * @test
     */
    public function wrongLocaleFormatException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Target locale must match the following format: "aa_AA".');
        new Locale('wrong_locale');
    }

    /**
     * @test
     */
    public function toStringConvert()
    {
        $locale = new Locale('de_DE');

        $this->assertEquals('de_DE', (string)$locale);
    }
}
