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

        $messageDefinition = $container->getDefinition('log.helper.message_provider');
        if (!empty($config['frequency'])) {
            $messageDefinition->replaceArgument(0, $config['frequency']);
        }
        if (!empty($config['logs_path'])) {
            $messageDefinition->replaceArgument(1, $config['logs_path']);
        }
        if (!empty($config['self_log_file_name'])) {
            $messageDefinition->replaceArgument(2, $config['self_log_file_name']);
        }
        if (!empty($config['logs'])) {
            $messageDefinition->replaceArgument(3, $config['logs']);
        }
        if (!empty($config['backup'])) {
            $messageDefinition->replaceArgument(4, $config['backup']);
        }
        if (!empty($config['remove_wait_days'])) {
            $messageDefinition->replaceArgument(5, (int)$config['remove_wait_days']);
        }
        if (!empty($config['backups']['logs_backup_path'])) {
            $messageDefinition->replaceArgument(6, $config['backups']['logs_backup_path']);
        }
        if (!empty($config['backups']['remove_backups'])) {
            $messageDefinition->replaceArgument(7, $config['backups']['remove_backups']);
        }
        if (!empty($config['backups']['remove_backups_wait_days'])) {
            $messageDefinition->replaceArgument(8, (int)$config['backups']['remove_backups_wait_days']);
        }
    }
}