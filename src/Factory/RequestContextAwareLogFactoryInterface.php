<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Factory;

use AssoConnect\LogBundle\Entity\Log;

interface RequestContextAwareLogFactoryInterface
{
    public function setRequestContext(Log $log): void;
}
