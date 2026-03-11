<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Log\Plugin\WebProfiler;

use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\WebProfilerExtension\Dependency\Plugin\WebProfilerDataCollectorPluginInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * @method \Spryker\Glue\Log\LogFactory getFactory()
 * @method \Spryker\Glue\Log\LogConfig getConfig()
 */
class WebProfilerAuditLogDataCollectorPlugin extends AbstractPlugin implements WebProfilerDataCollectorPluginInterface
{
    protected const string NAME = 'audit_log';

    protected const string TEMPLATE = '@Log/audit_log.html.twig';

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     */
    public function getTemplateName(): string
    {
        return static::TEMPLATE;
    }

    /**
     * {@inheritDoc}
     * - Discovers audit logger channels for the current application from the corresponding config key.
     * - Pushes a shared buffer handler onto each audit logger to capture records.
     * - Returns an `AuditLogDataCollector` with the buffer handler and discovered channel names.
     *
     * @api
     */
    public function getDataCollector(ContainerInterface $container): DataCollectorInterface
    {
        return $this->getFactory()->createAuditLogDataCollectorCreator()->createDataCollector();
    }
}
