<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Log\Strategy;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AuditLoggerConfigCriteriaTransfer;
use Spryker\Glue\Log\Plugin\Log\GlueSecurityAuditLoggerConfigPlugin;
use Spryker\Shared\Log\LogConstants;
use Spryker\Shared\Log\Strategy\GlueStorefrontAuditLoggerConfigPluginProviderStrategy;
use SprykerTest\Shared\Log\LogSharedTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Shared
 * @group Log
 * @group Strategy
 * @group GlueStorefrontAuditLoggerConfigPluginProviderStrategyTest
 * Add your own group annotations below this line
 */
class GlueStorefrontAuditLoggerConfigPluginProviderStrategyTest extends Unit
{
    protected const string CHANNEL_NAME_SECURITY = 'security';

    protected LogSharedTester $tester;

    public function testGivenGlueStorefrontApplicationWithConfiguredPluginsWhenIsApplicableIsCalledThenItIsApplicable(): void
    {
        // Arrange
        $this->tester->setConfig(LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_STOREFRONT, [GlueSecurityAuditLoggerConfigPlugin::class]);
        $strategy = $this->createStrategyForApplication('GLUE_STOREFRONT');

        // Act
        $isApplicable = $strategy->isApplicable();

        // Assert
        $this->assertTrue($isApplicable);
    }

    public function testGivenGlueApplicationWhenIsApplicableIsCalledThenItIsNotApplicable(): void
    {
        // Arrange
        $this->tester->setConfig(LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_STOREFRONT, [GlueSecurityAuditLoggerConfigPlugin::class]);
        $strategy = $this->createStrategyForApplication('GLUE');

        // Act
        $isApplicable = $strategy->isApplicable();

        // Assert
        $this->assertFalse($isApplicable);
    }

    public function testGivenSecurityChannelWhenProvidePluginIsCalledThenTheConfiguredSecurityPluginIsReturned(): void
    {
        // Arrange
        $this->tester->setConfig(LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_STOREFRONT, [GlueSecurityAuditLoggerConfigPlugin::class]);
        $strategy = $this->createStrategyForApplication('GLUE_STOREFRONT');

        // Act
        $auditLoggerConfigPlugin = $strategy->providePlugin(
            (new AuditLoggerConfigCriteriaTransfer())->setChannelName(static::CHANNEL_NAME_SECURITY),
        );

        // Assert
        $this->assertInstanceOf(GlueSecurityAuditLoggerConfigPlugin::class, $auditLoggerConfigPlugin);
    }

    protected function createStrategyForApplication(string $application): GlueStorefrontAuditLoggerConfigPluginProviderStrategy
    {
        return new class ($application) extends GlueStorefrontAuditLoggerConfigPluginProviderStrategy {
            public function __construct(protected string $application)
            {
            }

            protected function getApplication(): string
            {
                return $this->application;
            }
        };
    }
}
