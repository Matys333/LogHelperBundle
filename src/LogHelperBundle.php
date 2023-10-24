<?php

namespace Matys333\LogHelperBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LogHelperBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->arrayNode('backups')
            ->children()
            ->scalarNode('logs_path')->end()
            ->scalarNode('logs_backup_path')->end()
            ->scalarNode('self_log_file_name')->end()
            ->arrayNode('logs')->end()
            ->end()
            ->end() // backups
            ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->get('log.helper.message_handler')
            ->arg(0, $config['backups']['logs_path'])
            ->arg(1, $config['backups']['logs_backup_path'])
            ->arg(2, $config['backups']['self_log_file_name'])
            ->arg(3, $config['backups']['logs']);
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}