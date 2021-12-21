<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Service;

use AssoConnect\LogBundle\Entity\Log;
use AssoConnect\LogBundle\Factory\LogFactoryInterface;
use AssoConnect\LogBundle\Tests\Functional\Entity\FunctionalLog;
use Ramsey\Uuid\Uuid;

class LogFactory implements LogFactoryInterface
{
    public function createLogFromEntity(object $entity, string $entityColumn, string $oldValue = null): Log
    {
        return new FunctionalLog(
            Uuid::uuid1(),
            'entityClass',
            $entityColumn,
            $oldValue,
            'entityId',
            'requestTrace'
        );
    }
}
