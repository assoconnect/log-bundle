<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Factory;

use AssoConnect\LogBundle\Entity\Log;

interface SecurityContextAwareLogFactoryInterface
{
    public function setSecurityUser(Log $log): void;
}
