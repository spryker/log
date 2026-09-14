<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Handler;

use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Monolog\Handler\AbstractProcessingHandler;

class AbstractQueueHandler extends AbstractProcessingHandler
{
    /**
     * @var \Spryker\Client\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @param \Monolog\LogRecord|array $record
     */
    protected function write($record): void
    {
        $this->send(is_array($record) ? $record['formatted'] : $record->formatted);
    }

    public function handleBatch(array $records): void
    {
        // @phpstan-ignore function.impossibleType, function.alreadyNarrowedType
        $level = is_int($this->level) ? $this->level : $this->level->value;

        $records = array_filter($records, function ($record) use ($level) {
            // @phpstan-ignore function.impossibleType, function.alreadyNarrowedType
            $recordLevel = is_array($record) ? $record['level'] : $record->level->value;

            return ($recordLevel >= $level);
        });

        if ($records) {
            $this->send($this->getFormatter()->formatBatch($records));
        }
    }

    /**
     * @param string $data
     *
     * @return void
     */
    protected function send($data): void
    {
        $queueSendTransfer = new QueueSendMessageTransfer();
        $queueSendTransfer->setBody($data);

        $this->queueClient->sendMessage($this->queueName, $queueSendTransfer);
    }
}
