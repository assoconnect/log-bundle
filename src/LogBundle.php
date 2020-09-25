<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle;

use AssoConnect\LogBundle\DependencyInjection\LogExtension;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LogBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerForAutoconfiguration(EventSubscriber::class)
            ->addTag('doctrine.event_subscriber');
    }

    public function getContainerExtension()
    {
        return new LogExtension();
    }
}
