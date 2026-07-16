<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle;

use AssoConnect\LogBundle\DependencyInjection\LogExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LogBundle extends Bundle
{
    public function getContainerExtension(): LogExtension
    {
        return new LogExtension();
    }
}
