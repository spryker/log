<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Log\Plugin\Processor;

use Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \Spryker\Yves\Log\LogFactory getFactory()
 */
class ResponseProcessorPlugin extends AbstractPlugin implements LogProcessorPluginInterface
{
    /**
     * @param \Monolog\LogRecord|array<string, mixed> $data
     *
     * @return \Monolog\LogRecord|array
     */
    public function __invoke($data)
    {
        return $this->getFactory()->createResponseProcessorPublic()->__invoke($data);
    }
}
