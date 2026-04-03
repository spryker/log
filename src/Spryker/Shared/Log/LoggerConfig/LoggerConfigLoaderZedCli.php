<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\LoggerConfig;

use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Shared\Log\LogConstants;

class LoggerConfigLoaderZedCli implements LoggerConfigLoaderInterface
{
    protected const string SAPI_CLI = 'cli';

    protected const string SAPI_PHPDBG = 'phpdbg';

    public function accept(): bool
    {
        return $this->isCliRequest() && $this->isZedApplication() && Config::hasKey(LogConstants::LOGGER_CONFIG_ZED_CLI);
    }

    public function create(): LoggerConfigInterface
    {
        /** @phpstan-var class-string<\Spryker\Shared\Log\Config\LoggerConfigInterface> $loggerClassName */
        $loggerClassName = Config::get(LogConstants::LOGGER_CONFIG_ZED_CLI);

        return new $loggerClassName();
    }

    protected function isCliRequest(): bool
    {
        return static::SAPI_CLI === PHP_SAPI || static::SAPI_PHPDBG === PHP_SAPI;
    }

    protected function isZedApplication(): bool
    {
        return APPLICATION === 'ZED';
    }
}
