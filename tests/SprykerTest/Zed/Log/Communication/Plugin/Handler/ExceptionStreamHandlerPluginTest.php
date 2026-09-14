<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Log\Communication\Plugin\Handler;

use Codeception\Test\Unit;
use DateTimeImmutable;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Spryker\Zed\Log\Communication\Plugin\Handler\ExceptionStreamHandlerPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Log
 * @group Communication
 * @group Plugin
 * @group Handler
 * @group ExceptionStreamHandlerPluginTest
 * Add your own group annotations below this line
 */
class ExceptionStreamHandlerPluginTest extends Unit
{
    /**
     * @var string
     */
    public const FILENAME = 'exception.log';

    /**
     * @var \SprykerTest\Zed\Log\LogCommunicationTester
     */
    protected $tester;

    protected function getExceptionStreamHandlerPlugin(): ExceptionStreamHandlerPlugin
    {
        $this->tester->mockConfigMethod('getExceptionLogDestinationPath', function () {
            return $this->tester->getPathToLogFile(static::FILENAME);
        });

        $handler = new ExceptionStreamHandlerPlugin();
        $handler->setFactory($this->tester->getFactory());

        return $handler;
    }

    public function testIsHandlingReturnsTrueIfLogLevelIsError(): void
    {
        $record = $this->createLogRecord(Logger::ERROR);

        $this->assertTrue($this->getExceptionStreamHandlerPlugin()->isHandling($record));
    }

    public function testIsHandlingReturnsFalseIfLogLevelIsLowerThanError(): void
    {
        $record = $this->createLogRecord(Logger::DEBUG);

        $this->assertFalse($this->getExceptionStreamHandlerPlugin()->isHandling($record));
    }

    public function testHandleWritesMessageToLogFile(): void
    {
        $record = $this->createLogRecord(Logger::ERROR, 'Test exception logs');
        $this->getExceptionStreamHandlerPlugin()->handle($record);

        $this->tester->assertLogFileContains(static::FILENAME, 'Test exception logs');
    }

    public function testHandleBatchWritesMessagesToLogFile(): void
    {
        $records = [
            $this->createLogRecord(Logger::ERROR, 'Test exception logs'),
            $this->createLogRecord(Logger::ERROR, 'Test exception logs 2'),
        ];

        $this->getExceptionStreamHandlerPlugin()->handleBatch($records);

        $this->tester->assertLogFileContains(static::FILENAME, 'Test exception logs');
        $this->tester->assertLogFileContains(static::FILENAME, 'Test exception logs 2');
    }

    /**
     * @return \Monolog\LogRecord|array<string, mixed>
     */
    protected function createLogRecord(int $level, string $message = '')
    {
        if (class_exists(LogRecord::class)) {
            return new LogRecord(
                new DateTimeImmutable(),
                'test',
                Level::fromValue($level),
                $message,
            );
        }

        return ['level' => $level, 'extra' => [], 'context' => [], 'message' => $message];
    }

    public function testPushProcessorReturnsProcessableHandlerInterface(): void
    {
        $processableInterface = $this->getExceptionStreamHandlerPlugin()->pushProcessor(function () {
        });

        $this->assertInstanceOf(HandlerInterface::class, $processableInterface);
    }

    public function testPopProcessorReturnsAddedProcessor(): void
    {
        $processor = function () {
        };
        $handler = $this->getExceptionStreamHandlerPlugin();
        $handler->pushProcessor($processor);

        $this->assertSame($processor, $handler->popProcessor());
    }

    public function testSetFormatterReturnsFormattableHandlerInterface(): void
    {
        $formatterMock = $this->getFormatterMock();
        $formattableInterface = $this->getExceptionStreamHandlerPlugin()->setFormatter($formatterMock);

        $this->assertInstanceOf(HandlerInterface::class, $formattableInterface);
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFormatterMock(): FormatterInterface
    {
        return $this->getMockBuilder(FormatterInterface::class)->getMock();
    }

    public function testGetFormatterReturnsAddedFormatter(): void
    {
        $formatterMock = $this->getFormatterMock();
        $handler = $this->getExceptionStreamHandlerPlugin();
        $handler->setFormatter($formatterMock);

        $this->assertInstanceOf(FormatterInterface::class, $handler->getFormatter());
    }
}
