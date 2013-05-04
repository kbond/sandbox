<?php

namespace Zenstruck\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_media');

        $rootNode
            ->children()
                ->scalarNode('root_dir')->defaultValue('%kernel.root_dir%/../web/files')->end()
                ->scalarNode('web_prefix')->defaultValue('/files')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}