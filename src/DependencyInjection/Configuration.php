<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('log');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('log_filters')
                    ->children()
                        ->arrayNode('includedEntities')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('excludedEntities')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
