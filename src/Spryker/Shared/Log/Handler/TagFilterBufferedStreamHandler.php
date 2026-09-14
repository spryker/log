<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Handler;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\HandlerWrapper;
use Spryker\Shared\Log\Exception\InvalidLogRecordTagsTypeException;

class TagFilterBufferedStreamHandler extends HandlerWrapper
{
    /**
     * @var string
     */
    protected const RECORD_KEY_CONTEXT = 'context';

    /**
     * @var string
     */
    protected const RECORD_KEY_CONTEXT_TAGS = 'tags';

    /**
     * @var list<string>
     */
    protected array $tagDisallowList;

    /**
     * @param \Monolog\Handler\HandlerInterface $handler
     * @param list<string> $tagDisallowList
     */
    public function __construct(HandlerInterface $handler, array $tagDisallowList)
    {
        $this->tagDisallowList = $tagDisallowList;

        parent::__construct($handler);
    }

    /**
     * @param \Monolog\LogRecord|array $record
     */
    public function handle($record): bool
    {
        if ($this->isRecordDisallowedByTags($record)) {
            return false;
        }

        // @phpstan-ignore argument.type
        return $this->handler->handle($record);
    }

    /**
     * @param \Monolog\LogRecord|array $record
     */
    protected function isRecordDisallowedByTags($record): bool
    {
        $context = is_array($record) ? ($record[static::RECORD_KEY_CONTEXT] ?? []) : $record->context;

        if (!isset($context[static::RECORD_KEY_CONTEXT_TAGS])) {
            return false;
        }

        $this->assertRecordTagsType($context);

        if (array_intersect($context[static::RECORD_KEY_CONTEXT_TAGS], $this->tagDisallowList)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws \Spryker\Shared\Log\Exception\InvalidLogRecordTagsTypeException
     */
    protected function assertRecordTagsType(array $context): void
    {
        if (!is_array($context[static::RECORD_KEY_CONTEXT_TAGS])) {
            throw new InvalidLogRecordTagsTypeException(
                sprintf('Invalid log record context: The value of the "%s" key must be an array.', static::RECORD_KEY_CONTEXT_TAGS),
            );
        }
    }
}
