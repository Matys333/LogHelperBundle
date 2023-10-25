<?php

namespace Matys333\LogHelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface

{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('log_helper');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->arrayNode('backups')
            ->children()
            ->scalarNode('logs_path')->defaultNull()->end()
            ->scalarNode('logs_backup_path')->defaultNull()->end()
            ->scalarNode('self_log_file_name')->defaultNull()->end()
            ->scalarNode('logs')->defaultNull()->end()
            ->end()
            ->end() // backups
            ->end();

        return $treeBuilder;
    }
}