<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Service;

use AssoConnect\LogBundle\Entity\Log;
use AssoConnect\LogBundle\Factory\LogFactoryInterface;
use AssoConnect\LogBundle\Tests\Functional\Entity\FunctionalLog;

class LogFactory implements LogFactoryInterface
{
    public function createLogFromEntity(
        object $entity,
        string $entityColumn,
        ?string $entityOldValue,
        string $requestTrace,
    ): Log {
        return new FunctionalLog(
            $entity,
            $entityColumn,
            $entityOldValue,
            $requestTrace,
        );
    }
}
