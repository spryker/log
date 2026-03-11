<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\WebProfiler;

use Generated\Shared\Transfer\AuditLoggerConfigCriteriaTransfer;
use Monolog\Logger as MonologLogger;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\AuditLoggerTrait;
use Spryker\Shared\Log\DataCollector\AuditLogDataCollector;
use Spryker\Shared\Log\Handler\AuditLogBufferHandler;
use Spryker\Shared\LogExtension\Dependency\Plugin\AuditLoggerConfigPluginInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class AuditLogDataCollectorCreator implements AuditLogDataCollectorCreatorInterface
{
    use AuditLoggerTrait;

    /**
     * @param array<string, string> $applicationToConfigKeyMap Maps APPLICATION identifier to audit logger config plugins config key
     */
    public function __construct(protected readonly array $applicationToConfigKeyMap)
    {
    }

    /**
     * {@inheritDoc}
     * - Discovers audit logger channels for the current application from the corresponding config key.
     * - Pushes a shared buffer handler onto each audit logger to capture records.
     * - Returns an `AuditLogDataCollector` with the buffer handler and discovered channel names.
     */
    public function createDataCollector(): DataCollectorInterface
    {
        $bufferHandler = new AuditLogBufferHandler();
        $channelNames = $this->discoverChannelNames();

        foreach ($channelNames as $channelName) {
            $criteria = (new AuditLoggerConfigCriteriaTransfer())->setChannelName($channelName);
            $logger = $this->getAuditLogger($criteria);

            if ($logger instanceof MonologLogger) {
                $logger->pushHandler($bufferHandler);
            }
        }

        return new AuditLogDataCollector($bufferHandler, $channelNames);
    }

    /**
     * @return array<int, string>
     */
    protected function discoverChannelNames(): array
    {
        $configKey = $this->resolveConfigKey();

        if ($configKey === null || !Config::hasKey($configKey)) {
            return [];
        }

        $pluginClassNames = Config::get($configKey);
        $channelNames = [];

        foreach ($pluginClassNames as $pluginClassName) {
            $plugin = new $pluginClassName();

            if (!$plugin instanceof AuditLoggerConfigPluginInterface) {
                continue;
            }

            $channelNames[] = $plugin->getChannelName();
        }

        return $channelNames;
    }

    protected function resolveConfigKey(): ?string
    {
        if (!defined('APPLICATION')) {
            return null;
        }

        return $this->applicationToConfigKeyMap[APPLICATION] ?? null;
    }
}
