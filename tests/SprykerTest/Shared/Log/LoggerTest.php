<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Log;

use Codeception\Test\Unit;
use Psr\Log\LoggerInterface;
use Spryker\Shared\Log\LoggerTrait;
use SprykerTest\Shared\Log\Fixtures\TestLoggerConfig;
use SprykerTest\Shared\Log\Fixtures\TestLoggerConfig2;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Shared
 * @group Log
 * @group LoggerTest
 * Add your own group annotations below this line
 */
class LoggerTest extends Unit
{
    use LoggerTrait;

    public function testGetLoggerWithoutConfigShouldReturnDefaultLoggerInstance(): void
    {
        $this->assertInstanceOf(LoggerInterface::class, $this->getLogger());
    }

    public function testGetLoggerWithSameConfigShouldReturnTheSameLoggerInstance(): void
    {
        $logger1 = $this->getLogger(new TestLoggerConfig());
        $logger2 = $this->getLogger(new TestLoggerConfig());

        $this->assertSame($logger1, $logger2);
    }

    public function testGetLoggerWithDifferentConfigShouldReturnDifferentLoggerInstances(): void
    {
        $logger1 = $this->getLogger(new TestLoggerConfig());
        $logger2 = $this->getLogger(new TestLoggerConfig2());

        $this->assertNotSame($logger1, $logger2);
    }
}
