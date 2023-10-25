<?php

namespace Matys333\LogHelperBundle;

use Matys333\LogHelperBundle\DependencyInjection\LogHelperExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class LogHelperBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (empty($this->extension)) {
            $this->extension = new LogHelperExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}