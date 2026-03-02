<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Log;

use Generated\Shared\Transfer\AuditLoggerConfigCriteriaTransfer;
use Psr\Log\LoggerInterface;

trait AuditLoggerTrait
{
    protected function getAuditLogger(AuditLoggerConfigCriteriaTransfer $auditLoggerConfigCriteriaTransfer): LoggerInterface
    {
        return AuditLoggerFactory::getInstance($auditLoggerConfigCriteriaTransfer);
    }
}
