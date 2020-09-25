<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Factory;

use AssoConnect\LogBundle\Entity\Log;

interface LogFactoryInterface
{
    public function createLogFromEntity(Object $entity, string $entityColumn, string $oldValue = null): Log;
}
