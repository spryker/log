<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log\WebProfiler;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Creates a Web Profiler data collector that captures audit log records grouped by channel.
 */
interface AuditLogDataCollectorCreatorInterface
{
    public function createDataCollector(): DataCollectorInterface;
}
