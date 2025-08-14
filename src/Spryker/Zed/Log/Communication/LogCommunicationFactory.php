<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Log\Communication;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Spryker\Shared\Log\Handler\TagFilterBufferedStreamHandler;
use Spryker\Shared\Log\Processor\AuditLogMetaDataProcessor;
use Spryker\Shared\Log\Processor\EnvironmentProcessor;
use Spryker\Shared\Log\Processor\GuzzleBodyProcessor;
use Spryker\Shared\Log\Processor\ProcessorInterface;
use Spryker\Shared\Log\Processor\RequestProcessor;
use Spryker\Shared\Log\Processor\ResponseProcessor;
use Spryker\Shared\Log\Processor\ServerProcessor;
use Spryker\Shared\Log\Sanitizer\Sanitizer;
use Spryker\Shared\Log\Sanitizer\SanitizerInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Log\Communication\Handler\QueueHandler;
use Spryker\Zed\Log\Dependency\Facade\LogToLocaleFacadeInterface;
use Spryker\Zed\Log\LogDependencyProvider;

/**
 * @method \Spryker\Zed\Log\LogConfig getConfig()
 * @method \Spryker\Zed\Log\Business\LogFacadeInterface getFacade()
 */
class LogCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return array<\Monolog\Handler\HandlerInterface>
     */
    public function getHandlers()
    {
        return $this->getProvidedDependency(LogDependencyProvider::LOG_HANDLERS);
    }

    /**
     * @return array<callable>
     */
    public function getProcessors()
    {
        return $this->getProvidedDependency(LogDependencyProvider::LOG_PROCESSORS);
    }

    /**
     * @deprecated Use {@link createEnvironmentProcessorPublic()} instead.
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    protected function createEnvironmentProcessor()
    {
        return $this->createEnvironmentProcessorPublic();
    }

    /**
     * Deprecated: Will be renamed to createEnvironmentProcessor() in the next major release
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createEnvironmentProcessorPublic()
    {
        return new EnvironmentProcessor(
            $this->getLocaleFacade()->getCurrentLocaleName(),
        );
    }

    /**
     * @return \Spryker\Zed\Log\Dependency\Facade\LogToLocaleFacadeInterface
     */
    public function getLocaleFacade(): LogToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(LogDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @deprecated Use {@link createRequestProcessorPublic()} instead.
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    protected function createRequestProcessor()
    {
        return $this->createRequestProcessorPublic();
    }

    /**
     * Deprecated: Will be renamed to createRequestProcessor() in the next major release
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createRequestProcessorPublic()
    {
        return new RequestProcessor($this->createSanitizer());
    }

    /**
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createAuditLogRequestProcessor(): ProcessorInterface
    {
        return new RequestProcessor($this->createAuditLogSanitizer());
    }

    /**
     * @deprecated Use {@link createResponseProcessorPublic()} instead.
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    protected function createResponseProcessor()
    {
        return $this->createResponseProcessorPublic();
    }

    /**
     * Deprecated: Will be renamed to createResponseProcessor() in the next major release
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createResponseProcessorPublic()
    {
        return new ResponseProcessor();
    }

    /**
     * @deprecated Use {@link createServerProcessorPublic()} instead.
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    protected function createServerProcessor()
    {
        return $this->createServerProcessorPublic();
    }

    /**
     * Deprecated: Will be renamed to createServerProcessorPublic() in the next major release
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createServerProcessorPublic()
    {
        return new ServerProcessor();
    }

    /**
     * @deprecated Use {@link createGuzzleBodyProcessorPublic()} instead.
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    protected function createGuzzleBodyProcessor()
    {
        return new GuzzleBodyProcessor($this->createSanitizer());
    }

    /**
     * Deprecated: Will be renamed to createGuzzleBodyProcessor() in the next major release
     *
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createGuzzleBodyProcessorPublic()
    {
        return new GuzzleBodyProcessor($this->createSanitizer());
    }

    /**
     * @return \Monolog\Processor\PsrLogMessageProcessor
     */
    public function createPsrMessageProcessor()
    {
        return new PsrLogMessageProcessor();
    }

    /**
     * @return \Spryker\Shared\Log\Processor\ProcessorInterface
     */
    public function createAuditLogMetaDataProcessor(): ProcessorInterface
    {
        return new AuditLogMetaDataProcessor();
    }

    /**
     * @return \Spryker\Shared\Log\Sanitizer\SanitizerInterface
     */
    protected function createSanitizer()
    {
        return new Sanitizer(
            $this->getConfig()->getSanitizerFieldNames(),
            $this->getConfig()->getSanitizedFieldValue(),
        );
    }

    /**
     * @return \Spryker\Shared\Log\Sanitizer\SanitizerInterface
     */
    public function createAuditLogSanitizer(): SanitizerInterface
    {
        return new Sanitizer(
            $this->getConfig()->getAuditLogSanitizerFieldNames(),
            $this->getConfig()->getAuditLogSanitizedFieldValue(),
        );
    }

    /**
     * @param list<string> $auditLogTagDisallowList
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    public function createTagFilterBufferedStreamHandler(array $auditLogTagDisallowList = []): HandlerInterface
    {
        return new TagFilterBufferedStreamHandler($this->createBufferedStreamHandler(), $auditLogTagDisallowList);
    }

    /**
     * @return \Monolog\Handler\HandlerInterface|\Monolog\Handler\BufferHandler
     */
    public function createBufferedStreamHandler()
    {
        return new BufferHandler(
            $this->createStreamHandler(),
            $this->getConfig()->getBufferLimit(),
            $this->getConfig()->getLogLevel(),
            $this->getConfig()->getIsBubble(),
            $this->getConfig()->getIsFlushOnOverflow(),
        );
    }

    /**
     * @return \Monolog\Handler\HandlerInterface|\Monolog\Handler\StreamHandler
     */
    public function createStreamHandler()
    {
        $streamHandler = new StreamHandler(
            $this->getConfig()->getLogDestinationPath(),
            (int)$this->getConfig()->getLogLevel(),
        );

        $streamHandler->setFormatter($this->createLogstashFormatter());

        return $streamHandler;
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface|\Monolog\Formatter\LogstashFormatter
     */
    protected function createLogstashFormatter()
    {
        return new LogstashFormatter(APPLICATION);
    }

    /**
     * @return \Monolog\Handler\HandlerInterface|\Monolog\Handler\FilterHandler
     */
    public function createExceptionStreamHandler()
    {
        $streamHandler = new StreamHandler(
            $this->getConfig()->getExceptionLogDestinationPath(),
            Logger::ERROR,
        );
        $streamHandler->setFormatter($this->createExceptionFormatter());

        return $streamHandler;
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface|\Monolog\Formatter\LineFormatter
     */
    protected function createExceptionFormatter()
    {
        $lineFormatter = new LineFormatter();
        $lineFormatter->includeStacktraces(true);

        return $lineFormatter;
    }

    /**
     * @deprecated Use {@link createBufferedQueueHandlerPublic()} instead.
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function createBufferedQueueHandler()
    {
        return $this->createBufferedQueueHandlerPublic();
    }

    /**
     * Deprecated: Will be renamed to createBufferedQueueHandlerPublic()
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    public function createBufferedQueueHandlerPublic()
    {
        return new BufferHandler($this->createQueueHandler());
    }

    /**
     * @return \Monolog\Handler\HandlerInterface|\Spryker\Zed\Log\Communication\Handler\QueueHandler
     */
    protected function createQueueHandler()
    {
        return new QueueHandler(
            $this->getProvidedDependency(LogDependencyProvider::CLIENT_QUEUE),
            $this->getConfig()->getQueueName(),
        );
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface>
     */
    public function getZedSecurityAuditLogHandlerPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_ZED_SECURITY_AUDIT_LOG_HANDLER);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface>
     */
    public function getMerchantPortalSecurityAuditLogHandlerPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_MERCHANT_PORTAL_SECURITY_AUDIT_LOG_HANDLER);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface>
     */
    public function getZedSecurityAuditLogProcessorPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_ZED_SECURITY_AUDIT_LOG_PROCESSOR);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface>
     */
    public function getMerchantPortalSecurityAuditLogProcessorPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_MERCHANT_PORTAL_SECURITY_AUDIT_LOG_PROCESSOR);
    }
}
