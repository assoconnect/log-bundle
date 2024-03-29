<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\DependencyInjection;

use AssoConnect\LogBundle\Factory\LogDataFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class LogExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(LogDataFactory::class);
        $definition->replaceArgument('$includedEntities', $config['log_filters']['includedEntities']);
        $definition->replaceArgument('$excludedEntities', $config['log_filters']['excludedEntities']);
    }
}
