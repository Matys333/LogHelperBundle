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
        $definition = $container->getDefinition('log.helper.message_handler');

        if (!empty($config['logs_path'])) {
            $definition->replaceArgument(0, $config['logs_path']);
        }
        if (!empty($config['logs_backup_path'])) {
            $definition->replaceArgument(1, $config['logs_backup_path']);
        }
        if (!empty($config['self_log_file_name'])) {
            $definition->replaceArgument(2, $config['self_log_file_name']);
        }
        if (!empty($config['logs'])) {
            $definition->replaceArgument(3, $config['logs']);
        }
    }
}