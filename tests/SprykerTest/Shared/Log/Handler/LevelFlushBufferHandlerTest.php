<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Log\Handler;

use Codeception\Test\Unit;
use DateTimeImmutable;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Spryker\Shared\Log\Handler\LevelFlushBufferHandler;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Shared
 * @group Log
 * @group Handler
 * @group LevelFlushBufferHandlerTest
 * Add your own group annotations below this line
 */
class LevelFlushBufferHandlerTest extends Unit
{
    /**
     * The behaviour this handler exists for: a record at the flush level reaches the wrapped handler
     * before the process has any chance to reach shutdown. A plain BufferHandler does not.
     *
     * @return void
     */
    public function testFlushesImmediatelyOnARecordAtTheFlushLevel(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::DEBUG, true, false, Logger::CRITICAL);

        $handler->handle($this->createRecord(Logger::CRITICAL, 'gateway exploded'));

        $this->assertTrue($target->hasCriticalRecords(), 'CRITICAL record should have been written already');
        $this->assertCount(1, $target->getRecords());
    }

    /**
     * Negative control: the same record through a plain BufferHandler is still held in memory.
     * Without this, the test above would pass even if the subclass did nothing.
     *
     * @return void
     */
    public function testPlainBufferHandlerStillHoldsTheSameRecord(): void
    {
        $target = new TestHandler();
        $handler = new BufferHandler($target, 0, Logger::DEBUG, true, false);

        $handler->handle($this->createRecord(Logger::CRITICAL, 'gateway exploded'));

        $this->assertCount(0, $target->getRecords(), 'BufferHandler is expected to still be buffering');
    }

    public function testKeepsBufferingRecordsBelowTheFlushLevel(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::DEBUG, true, false, Logger::CRITICAL);

        $handler->handle($this->createRecord(Logger::INFO, 'ordinary'));
        $handler->handle($this->createRecord(Logger::WARNING, 'also ordinary'));

        $this->assertCount(0, $target->getRecords());

        $handler->close();

        $this->assertCount(2, $target->getRecords(), 'close() should still flush what was buffered');
    }

    /**
     * The context leading up to a failure is worth as much as the failure itself, so records buffered
     * before the flushing record must go out with it rather than waiting for shutdown.
     *
     * @return void
     */
    public function testFlushesPreviouslyBufferedRecordsAlongWithTheFlushingRecord(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::DEBUG, true, false, Logger::CRITICAL);

        $handler->handle($this->createRecord(Logger::INFO, 'context before the failure'));
        $handler->handle($this->createRecord(Logger::CRITICAL, 'the failure'));

        $records = $target->getRecords();

        $this->assertCount(2, $records);
        $this->assertSame('context before the failure', $records[0]['message']);
        $this->assertSame('the failure', $records[1]['message']);
    }

    public function testDoesNotHandleRecordsBelowTheHandlerLevel(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::ERROR, true, false, Logger::CRITICAL);

        $handler->handle($this->createRecord(Logger::INFO, 'below the handler level'));
        $handler->close();

        $this->assertCount(0, $target->getRecords());
    }

    public function testFlushLevelIsConfigurable(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::DEBUG, true, false, Logger::WARNING);

        $handler->handle($this->createRecord(Logger::WARNING, 'flushes at warning here'));

        $this->assertCount(1, $target->getRecords());
    }

    /**
     * A null flush level is the opt-out: the handler must behave exactly like a plain BufferHandler,
     * buffering everything until shutdown.
     *
     * @return void
     */
    public function testDoesNotFlushEarlyWhenFlushLevelIsNull(): void
    {
        $target = new TestHandler();
        $handler = new LevelFlushBufferHandler($target, 0, Logger::DEBUG, true, false, null);

        $handler->handle($this->createRecord(Logger::CRITICAL, 'stays buffered'));

        $this->assertCount(0, $target->getRecords(), 'a null flush level must not flush early');

        $handler->close();

        $this->assertCount(1, $target->getRecords(), 'close() must still flush what was buffered');
    }

    /**
     * @param int $level
     * @param string $message
     *
     * @return array<string, mixed>
     */
    protected function createRecord(int $level, string $message): array
    {
        return [
            'message' => $message,
            'context' => [],
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => new DateTimeImmutable(),
            'extra' => [],
        ];
    }
}
