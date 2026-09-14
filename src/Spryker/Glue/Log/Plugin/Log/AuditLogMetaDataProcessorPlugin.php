<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Log\Plugin\Log;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface;

/**
 * @method \Spryker\Glue\Log\LogFactory getFactory()
 * @method \Spryker\Glue\Log\LogConfig getConfig()
 */
class AuditLogMetaDataProcessorPlugin extends AbstractPlugin implements LogProcessorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Adds `audit_log` log type to the data.
     *
     * @api
     *
     * @param \Monolog\LogRecord|array<string, mixed> $data
     *
     * @return \Monolog\LogRecord|array<string, mixed>
     */
    public function __invoke($data)
    {
        return $this->getFactory()->createAuditLogMetaDataProcessor()->__invoke($data);
    }
}
