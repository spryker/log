<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Handler;

use Monolog\Handler\AbstractHandler;
use Monolog\Logger;

class AuditLogBufferHandler extends AbstractHandler
{
    /**
     * @var array<int, array<string, mixed>|\Monolog\LogRecord>
     */
    protected array $records = [];

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct(Logger::DEBUG, true);
    }

    /**
     * {@inheritDoc}
     *
     * @param \Monolog\LogRecord|array $record
     */
    public function handle($record): bool
    {
        $this->records[] = $record;

        // Return false to not stop propagation to other handlers
        return false;
    }

    /**
     * @return array<int, array<string, mixed>|\Monolog\LogRecord>
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function reset(): void
    {
        $this->records = [];
    }
}
