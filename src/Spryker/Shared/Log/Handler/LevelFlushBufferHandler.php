<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Handler;

use Monolog\Handler\BufferHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * A buffering handler that stops buffering as soon as it sees a record worth keeping.
 *
 * `BufferHandler` holds every record in memory and writes them on shutdown. That is the right trade for
 * ordinary traffic, and it silently loses everything when the process does not reach shutdown: a php-fpm
 * worker killed mid-request, an OOM kill, a request termination timeout, an instance interruption. The one
 * record anybody needs in that situation is the one that is lost.
 *
 * This handler keeps the buffering, and flushes immediately once a record at or above `$flushLevel`
 * arrives. Records buffered before it are flushed with it, so the context leading up to the failure
 * survives too. Below that level nothing changes.
 *
 * Passing `null` as `$flushLevel` disables the early flush entirely, leaving the stock `BufferHandler`
 * behaviour of writing only on shutdown.
 *
 * @phpstan-import-type Level from \Monolog\Logger
 * @phpstan-import-type LevelName from \Monolog\Logger
 * @phpstan-import-type Record from \Monolog\Logger
 */
class LevelFlushBufferHandler extends BufferHandler
{
    /**
     * @phpstan-var Level|null
     */
    protected ?int $flushLevel;

    /**
     * @phpstan-param Level|LevelName|\Psr\Log\LogLevel::* $level
     * @phpstan-param Level|LevelName|\Psr\Log\LogLevel::*|null $flushLevel
     *
     * @param \Monolog\Handler\HandlerInterface $handler
     * @param int $bufferLimit
     * @param string|int $level
     * @param bool $bubble
     * @param bool $flushOnOverflow
     * @param string|int|null $flushLevel Records at or above this level flush the buffer at once.
     *     `null` disables the early flush and keeps the stock buffered behaviour.
     */
    public function __construct(
        HandlerInterface $handler,
        int $bufferLimit = 0,
        $level = Logger::DEBUG,
        bool $bubble = true,
        bool $flushOnOverflow = false,
        $flushLevel = Logger::CRITICAL
    ) {
        parent::__construct($handler, $bufferLimit, $level, $bubble, $flushOnOverflow);

        $this->flushLevel = $flushLevel === null ? null : Logger::toMonologLevel($flushLevel);
    }

    /**
     * {@inheritDoc}
     *
     * @phpstan-param Record $record
     *
     * @param array<string, mixed> $record
     *
     * @return bool
     */
    public function handle(array $record): bool
    {
        $result = parent::handle($record);

        if ($this->flushLevel !== null && $record['level'] >= $this->flushLevel) {
            $this->flush();
        }

        return $result;
    }
}
