<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Log\Plugin\Handler;

use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Monolog\Handler\HandlerInterface;

/**
 * @method \Spryker\Yves\Log\LogFactory getFactory()
 */
class QueueHandlerPlugin extends AbstractHandlerPlugin
{
    protected function getHandler(): HandlerInterface
    {
        if (!$this->handler) {
            $this->handler = $this->getFactory()->createBufferedQueueHandlerPublic();
        }

        return $this->handler;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Monolog\LogRecord|array $record
     */
    public function isHandling($record): bool
    {
        if (!class_exists(QueueSendMessageTransfer::class)) {
            return false;
        }

        return parent::isHandling($record);
    }
}
