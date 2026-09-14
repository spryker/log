<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\DataCollector;

use Monolog\Logger;
use Spryker\Shared\Log\Handler\AuditLogBufferHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Throwable;

class AuditLogDataCollector extends DataCollector implements LateDataCollectorInterface
{
    protected const string COLLECTOR_NAME = 'audit_log';

    protected const string KEY_LOGS_BY_CHANNEL = 'logs_by_channel';

    protected const string KEY_CHANNEL_NAMES = 'channel_names';

    protected const string KEY_ERROR_COUNT = 'error_count';

    protected const string KEY_CHANNEL = 'channel';

    protected const string KEY_LEVEL_NAME = 'level_name';

    protected const string KEY_DATETIME = 'datetime';

    protected const string KEY_LEVEL = 'level';

    protected const string KEY_MESSAGE = 'message';

    protected const string KEY_CONTEXT = 'context';

    protected const string KEY_TIME = 'time';

    protected const string KEY_LEVEL_VALUE = 'level_value';

    protected const string TIME_FORMAT = 'H:i:s.u';

    /**
     * @param array<int, string> $channelNames
     */
    public function __construct(
        protected AuditLogBufferHandler $handler,
        protected array $channelNames,
    ) {
    }

    public function getName(): string
    {
        return static::COLLECTOR_NAME;
    }

    /**
     * {@inheritDoc}
     * - No-op: records are captured by the buffer handler during request processing.
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
    }

    /**
     * {@inheritDoc}
     * - Groups buffered audit log records by channel and stores them for panel rendering.
     */
    public function lateCollect(): void
    {
        $logsByChannel = [];
        $errorCount = 0;

        foreach ($this->handler->getRecords() as $record) {
            if (is_array($record)) {
                $channel = $record[static::KEY_CHANNEL];
                $levelName = $record[static::KEY_LEVEL_NAME];
                $levelValue = $record[static::KEY_LEVEL];
                $datetime = $record[static::KEY_DATETIME];
                $message = $record[static::KEY_MESSAGE];
                $context = $record[static::KEY_CONTEXT];
            } else {
                $channel = $record->channel;
                $levelName = $record->level->getName();
                $levelValue = $record->level->value;
                $datetime = $record->datetime;
                $message = $record->message;
                $context = $record->context;
            }

            if ($levelValue >= Logger::ERROR) {
                $errorCount++;
            }

            $logsByChannel[$channel][] = [
                static::KEY_TIME => $datetime->format(static::TIME_FORMAT),
                static::KEY_CHANNEL => $channel,
                static::KEY_LEVEL => $levelName,
                static::KEY_LEVEL_VALUE => $levelValue,
                static::KEY_MESSAGE => $message,
                static::KEY_CONTEXT => $this->cloneVar($context),
            ];
        }

        $this->data = [
            static::KEY_LOGS_BY_CHANNEL => $logsByChannel,
            static::KEY_CHANNEL_NAMES => $this->channelNames,
            static::KEY_ERROR_COUNT => $errorCount,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLogs(): array
    {
        $logsByChannel = $this->data[static::KEY_LOGS_BY_CHANNEL];

        if ($logsByChannel === []) {
            return [];
        }

        return array_merge(...array_values($logsByChannel));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLogsByChannel(string $channel): array
    {
        return $this->data[static::KEY_LOGS_BY_CHANNEL][$channel] ?? [];
    }

    /**
     * @return array<int, string>
     */
    public function getChannels(): array
    {
        return $this->data[static::KEY_CHANNEL_NAMES];
    }

    public function getLogsCount(): int
    {
        return count($this->getLogs());
    }

    public function getErrorCount(): int
    {
        return $this->data[static::KEY_ERROR_COUNT];
    }
}
