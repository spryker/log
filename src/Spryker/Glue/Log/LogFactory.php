<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\Log;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface as MonologProcessorInterface;
use Monolog\Processor\PsrLogMessageProcessor;
use Spryker\Glue\Kernel\AbstractFactory;
use Spryker\Glue\Log\Dependency\Client\LogToLocaleClientInterface;
use Spryker\Glue\Log\Handler\QueueHandler;
use Spryker\Shared\Log\Handler\LevelFlushBufferHandler;
use Spryker\Shared\Log\Handler\TagFilterBufferedStreamHandler;
use Spryker\Shared\Log\LogConstants;
use Spryker\Shared\Log\Processor\AuditLogMetaDataProcessor;
use Spryker\Shared\Log\Processor\EnvironmentProcessor;
use Spryker\Shared\Log\Processor\GuzzleBodyProcessor;
use Spryker\Shared\Log\Processor\ProcessorInterface;
use Spryker\Shared\Log\Processor\RequestProcessor;
use Spryker\Shared\Log\Processor\ResponseProcessor;
use Spryker\Shared\Log\Processor\ServerProcessor;
use Spryker\Shared\Log\Sanitizer\Sanitizer;
use Spryker\Shared\Log\Sanitizer\SanitizerInterface;
use Spryker\Shared\Log\WebProfiler\AuditLogDataCollectorCreator;
use Spryker\Shared\Log\WebProfiler\AuditLogDataCollectorCreatorInterface;

/**
 * @method \Spryker\Glue\Log\LogConfig getConfig()
 */
class LogFactory extends AbstractFactory
{
    /**
     * @see \public/Glue/index.php
     */
    protected const string APPLICATION_GLUE = 'GLUE';

    /**
     * @see \public/GlueBackend/index.php
     */
    protected const string APPLICATION_GLUE_BACKEND = 'GLUE_BACKEND';

    public function createAuditLogDataCollectorCreator(): AuditLogDataCollectorCreatorInterface
    {
        return new AuditLogDataCollectorCreator([
            static::APPLICATION_GLUE => LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE,
            static::APPLICATION_GLUE_BACKEND => LogConstants::AUDIT_LOGGER_CONFIG_PLUGINS_GLUE_BACKEND,
        ]);
    }

    /**
     * @return array<\Monolog\Handler\HandlerInterface>
     */
    public function getHandlers(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::LOG_HANDLERS);
    }

    /**
     * @return array<callable>
     */
    public function getProcessors(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::LOG_PROCESSORS);
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

    public function createBufferedQueueHandler(): HandlerInterface
    {
        return new BufferHandler($this->createQueueHandler());
    }

    public function createQueueHandler(): HandlerInterface
    {
        return new QueueHandler(
            $this->getProvidedDependency(LogDependencyProvider::CLIENT_QUEUE),
            $this->getConfig()->getQueueName(),
        );
    }

    public function createBufferedStreamHandler(): HandlerInterface
    {
        return new LevelFlushBufferHandler($this->createStreamHandler());
    }

    public function createStreamHandler(): HandlerInterface
    {
        $streamHandler = new StreamHandler(
            $this->getConfig()->getLogDestinationPath(),
            $this->getConfig()->getLogLevel(),
        );

        $streamHandler->setFormatter($this->createLogstashFormatter());

        return $streamHandler;
    }

    public function createLogstashFormatter(): FormatterInterface
    {
        return new LogstashFormatter(APPLICATION);
    }

    public function createExceptionStreamHandler(): HandlerInterface
    {
        $streamHandler = new StreamHandler(
            $this->getConfig()->getExceptionLogDestinationPath(),
            Logger::ERROR,
        );
        $streamHandler->setFormatter($this->createExceptionFormatter());

        return $streamHandler;
    }

    public function createExceptionFormatter(): FormatterInterface
    {
        $lineFormatter = new LineFormatter();
        $lineFormatter->includeStacktraces(true);

        return $lineFormatter;
    }

    public function createEnvironmentProcessor(): ProcessorInterface
    {
        return new EnvironmentProcessor(
            $this->getLocaleClient()->getCurrentLocale(),
        );
    }

    public function getLocaleClient(): LogToLocaleClientInterface
    {
        return $this->getProvidedDependency(LogDependencyProvider::CLIENT_LOCALE);
    }

    public function createGuzzleBodyProcessor(): ProcessorInterface
    {
        return new GuzzleBodyProcessor($this->createSanitizer());
    }

    public function createPsrMessageProcessor(): MonologProcessorInterface
    {
        return new PsrLogMessageProcessor();
    }

    public function createRequestProcessor(): ProcessorInterface
    {
        return new RequestProcessor($this->createSanitizer());
    }

    public function createAuditLogRequestProcessor(): ProcessorInterface
    {
        return new RequestProcessor($this->createAuditLogSanitizer());
    }

    public function createResponseProcessor(): ProcessorInterface
    {
        return new ResponseProcessor();
    }

    public function createServerProcessor(): ProcessorInterface
    {
        return new ServerProcessor();
    }

    public function createAuditLogMetaDataProcessor(): ProcessorInterface
    {
        return new AuditLogMetaDataProcessor();
    }

    public function createSanitizer(): SanitizerInterface
    {
        return new Sanitizer(
            $this->getConfig()->getSanitizerFieldNames(),
            $this->getConfig()->getSanitizedFieldValue(),
        );
    }

    public function createAuditLogSanitizer(): SanitizerInterface
    {
        return new Sanitizer(
            $this->getConfig()->getAuditLogSanitizerFieldNames(),
            $this->getConfig()->getAuditLogSanitizedFieldValue(),
        );
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface>
     */
    public function getGlueSecurityAuditLogHandlerPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_GLUE_SECURITY_AUDIT_LOG_HANDLER);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogHandlerPluginInterface>
     */
    public function getGlueBackendSecurityAuditLogHandlerPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_GLUE_BACKEND_SECURITY_AUDIT_LOG_HANDLER);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface>
     */
    public function getGlueSecurityAuditLogProcessorPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_GLUE_SECURITY_AUDIT_LOG_PROCESSOR);
    }

    /**
     * @return list<\Spryker\Shared\Log\Dependency\Plugin\LogProcessorPluginInterface>
     */
    public function getGlueBackendSecurityAuditLogProcessorPlugins(): array
    {
        return $this->getProvidedDependency(LogDependencyProvider::PLUGINS_GLUE_BACKEND_SECURITY_AUDIT_LOG_PROCESSOR);
    }
}
