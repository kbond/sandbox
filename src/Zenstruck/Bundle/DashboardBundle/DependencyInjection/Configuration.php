<?php

namespace Zenstruck\Bundle\DashboardBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_dashboard');

        $rootNode
            ->children()
                ->scalarNode('title')->defaultValue('Administration')->end()
                ->scalarNode('layout')->defaultValue('ZenstruckDashboardBundle:Twitter:layout.html.twig')->end()
                ->arrayNode('menu')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('group')->defaultNull()->end()
                            ->scalarNode('icon')->defaultNull()->end()
                            ->booleanNode('nested')->defaultTrue()->end()
                            ->arrayNode('items')
                                ->useAttributeAsKey('name')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('uri')->defaultNull()->end()
                                        ->scalarNode('route')->defaultNull()->end()
                                        ->variableNode('routeParameters')->end()
                                        ->scalarNode('role')->defaultNull()->end()
                                        ->scalarNode('icon')->defaultNull()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
