<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Strategy;

use Generated\Shared\Transfer\AuditLoggerConfigCriteriaTransfer;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Log\LogConstants;
use Spryker\Shared\LogExtension\Dependency\Plugin\AuditLoggerConfigPluginInterface;

class GlueStorefrontAuditLoggerConfigPluginProviderStrategy extends AbstractAuditLoggerConfigPluginProviderStrategy
{
    /**
     * @see \public/GlueStorefront/index.php
     */
    protected const string APPLICATION_GLUE_STOREFRONT = 'GLUE_STOREFRONT';

    public function isApplicable(): bool
    {
        return $this->getApplication() === static::APPLICATION_GLUE_STOREFRONT
            && Config::hasKey(LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_STOREFRONT);
    }

    public function providePlugin(
        AuditLoggerConfigCriteriaTransfer $auditLoggerConfigCriteriaTransfer
    ): ?AuditLoggerConfigPluginInterface {
        $auditLoggerConfigPluginClassNames = Config::get(LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_STOREFRONT);

        return $this->resolvePlugin($auditLoggerConfigPluginClassNames, $auditLoggerConfigCriteriaTransfer);
    }

    protected function getApplication(): string
    {
        return APPLICATION;
    }
}
