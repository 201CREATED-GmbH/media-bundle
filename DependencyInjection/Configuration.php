<?php

namespace C201\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('c201_media');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('base_upload_path')->defaultValue('%kernel.root_dir%/../public/uploads')->end()
                ->arrayNode('objects')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('upload_path')->isRequired()->end()
                        ->arrayNode('medias')
                            ->isRequired()
                            ->prototype('array')
                            ->children()
                                ->arrayNode('liip_imagine_filters')
                                    ->defaultValue([])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('storage')
                                    ->children()
                                        ->scalarNode('backend')->defaultValue('file')->end()
                                        ->arrayNode('options')
                                            ->children()
                                                ->scalarNode('context')->defaultValue('')->end()
                                                ->booleanNode('controller')->defaultValue(false)->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('constraints')
                                    ->defaultValue(array())
                                    ->prototype('array')
                                    ->children()
                                        ->variableNode('options')->end()
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
