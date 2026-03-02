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

    protected function write(array $record): void
    {
        $this->send($record['formatted']);
    }

    public function handleBatch(array $records): void
    {
        $level = $this->level;

        $records = array_filter($records, function ($record) use ($level) {
            return ($record['level'] >= $level);
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
