<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\Processor;

interface ProcessorInterface
{
    /**
     * @param \Monolog\LogRecord|array<string, mixed> $data
     *
     * @return \Monolog\LogRecord|array
     */
    public function __invoke($data);
}
