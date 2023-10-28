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
                ->scalarNode('frequency')->defaultValue('1 day')->end()
                ->scalarNode('logs_path')->defaultValue('var/log/')->end()
                ->scalarNode('self_log_file_name')->defaultValue('log_helper')->end()
                ->scalarNode('logs')->defaultValue('dev, prod')->end()
                ->booleanNode('backup')->defaultTrue()->end()
                ->integerNode('remove_wait_days')->defaultValue(7)->end()
                    ->arrayNode('backups')
                        ->children()
                            ->scalarNode('logs_backup_path')->defaultValue('var/log/backup/')->end()
                            ->booleanNode('remove_backups')->defaultTrue()->end()
                            ->integerNode('remove_backups_wait_days')->defaultValue(28)->end()
                        ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}