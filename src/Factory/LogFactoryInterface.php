<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Factory;

use AssoConnect\LogBundle\Entity\Log;

/**
 * Factory called in the LoggerSubscriber to create the Log entity.
 */
interface LogFactoryInterface
{
    public function createLogFromEntity(object $entity, string $entityColumn, string $oldValue = null): Log;
}
