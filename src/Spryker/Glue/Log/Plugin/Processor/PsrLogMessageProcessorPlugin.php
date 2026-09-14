<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Log\Plugin\Processor;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface;

/**
 * @method \Spryker\Glue\Log\LogFactory getFactory()
 */
class PsrLogMessageProcessorPlugin extends AbstractPlugin implements LogProcessorPluginInterface
{
    /**
     * @param \Monolog\LogRecord|array<mixed> $data
     *
     * @return \Monolog\LogRecord|array
     */
    public function __invoke($data)
    {
        // @phpstan-ignore argument.type
        return $this->getFactory()->createPsrMessageProcessor()->__invoke($data);
    }
}
