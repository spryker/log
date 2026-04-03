<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Log\Communication\Plugin;

use Spryker\Shared\Log\Config\LoggerConfigInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Log\Communication\LogCommunicationFactory getFactory()
 * @method \Spryker\Zed\Log\LogConfig getConfig()
 * @method \Spryker\Zed\Log\Business\LogFacadeInterface getFacade()
 */
class ZedCliLoggerConfigPlugin extends AbstractPlugin implements LoggerConfigInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getChannelName()
    {
        return $this->getConfig()->getChannelName();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<\Monolog\Handler\HandlerInterface>
     */
    public function getHandlers()
    {
        return $this->getFactory()->getCliHandlers();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<callable>
     */
    public function getProcessors()
    {
        return $this->getFactory()->getProcessors();
    }
}
