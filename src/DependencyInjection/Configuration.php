<?php

namespace Matys333\LogHelperBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('log_helper');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('logs_path')->defaultValue('var/log/')->end()
            ->scalarNode('logs_backup_path')->defaultValue('var/log/backup')->end()
            ->scalarNode('self_log_file_name')->defaultValue('log_helper')->end()
            ->scalarNode('logs')->defaultValue('dev, prod')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}