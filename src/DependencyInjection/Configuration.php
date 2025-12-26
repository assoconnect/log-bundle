<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('log');

        /** @phpstan-ignore class.notFound */
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('log_filters')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('includedEntities')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('excludedEntities')
                            ->defaultValue([])
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
