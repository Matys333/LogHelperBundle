<?php

namespace Matys333\LogHelperBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class LogHelperExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loaderXml = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loaderXml->load('services.xml');
        $loaderYaml = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loaderYaml->load('services.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $scheduleProviderDefinition = $container->getDefinition('log.helper.schedule_provider');
        $messageHandlerDefinition = $container->getDefinition('log.helper.message_handler');
        // Schedule provider
        if (!empty($config['frequency'])) {
            $scheduleProviderDefinition->replaceArgument(0, $config['frequency']);
        }
        // Message handler
        if (!empty($config['logs_path'])) {
            $messageHandlerDefinition->replaceArgument(0, $config['logs_path']);
        }
        if (!empty($config['self_log_file_name'])) {
            $messageHandlerDefinition->replaceArgument(1, $config['self_log_file_name']);
        }
        if (!empty($config['logs'])) {
            $messageHandlerDefinition->replaceArgument(2, $config['logs']);
        }
        if (!empty($config['backup'])) {
            $messageHandlerDefinition->replaceArgument(3, $config['backup']);
        }
        if (!empty($config['remove_wait_days'])) {
            $messageHandlerDefinition->replaceArgument(4, (int)$config['remove_wait_days']);
        }

        if (!empty($config['backups']['logs_backup_path'])) {
            $messageHandlerDefinition->replaceArgument(5, $config['backups']['logs_backup_path']);
        }
        if (!empty($config['backups']['remove_backups'])) {
            $messageHandlerDefinition->replaceArgument(6, $config['backups']['remove_backups']);
        }
        if (!empty($config['backups']['remove_backups_wait_days'])) {
            $messageHandlerDefinition->replaceArgument(7, (int)$config['backups']['remove_backups_wait_days']);
        }
    }
}