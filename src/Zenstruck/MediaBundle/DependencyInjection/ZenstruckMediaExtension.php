<?php

namespace Zenstruck\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config['filesystems'] as $name => $filesystemConfig) {
            $definition = new Definition(
                $container->getParameter('zenstruck_media.filesystem_manager.class'),
                array(
                    $name,
                    $filesystemConfig['root_dir'],
                    $filesystemConfig['web_prefix'],
                    $container->getDefinition('zenstruck_media.alert_provider')
                )
            );
            $definition->setPublic(false);

            $container->setDefinition('zenstruck_media.filesystem_manager.'.$name, $definition);
            $container->getDefinition('zenstruck_media.filesystem_factory')
                ->addMethodCall('addManager', array($name, $definition))
            ;
        }
    }
}