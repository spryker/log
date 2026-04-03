<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Log\Plugin\Handler;

use Monolog\Handler\HandlerInterface;

/**
 * @method \Spryker\Yves\Log\LogFactory getFactory()
 */
class NonBufferedStreamHandlerPlugin extends AbstractHandlerPlugin
{
    protected function getHandler(): HandlerInterface
    {
        if (!$this->handler) {
            $this->handler = $this->getFactory()->createStreamHandler();
        }

        return $this->handler;
    }
}
