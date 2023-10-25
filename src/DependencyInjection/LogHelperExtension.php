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
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!empty($config['backups'])) {
            $container->setParameter('log_helper.backups.logs_path', $config['backups']['logs_path']);
            $container->setParameter('log_helper.backups.logs_backup_path', $config['backups']['logs_backup_path']);
            $container->setParameter('log_helper.backups.self_log_file_name', $config['backups']['self_log_file_name']);
            $container->setParameter('log_helper.backups.logs', $config['backups']['logs']);
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');
    }

    public function getAlias(): string
    {
        return "log_helper";
    }
}