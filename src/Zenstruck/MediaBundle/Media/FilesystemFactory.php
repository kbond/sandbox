<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zenstruck\MediaBundle\Media\Alert\AlertProviderInterface;
use Zenstruck\MediaBundle\Media\Permission\PermissionProviderInterface;
use Zenstruck\MediaBundle\Media\Permission\TruePermissionProvider;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactory
{
    const FILESYSTEM_MANAGER_CLASS  = 'Zenstruck\MediaBundle\Media\FilesystemManager';
    const FILESYSTEM_CLASS          = 'Zenstruck\MediaBundle\Media\Filesystem';

    protected $alerts;
    protected $permissions;
    protected $managers = array();

    public function __construct(AlertProviderInterface $alerts)
    {
        $this->alerts = $alerts;
        $this->permissions = new TruePermissionProvider();
    }

    public function setPermissionProvider(PermissionProviderInterface $provider)
    {
        $this->permissions = $provider;
    }

    public function addManager($name, array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array('root_dir', 'web_prefix', 'filesystem_manager_class', 'filesystem_class'));
        $resolver->setDefaults(array(
                'filesystem_manager_class' => static::FILESYSTEM_MANAGER_CLASS,
                'filesystem_class'=> static::FILESYSTEM_CLASS
            )
        );

        $this->managers[$name] = $resolver->resolve($config);
    }

    /**
     * @param Request|string|null $name
     * @param string $path
     *
     * @return FilesystemManager
     */
    public function getManager($name = null, $path = null)
    {
        $managers = $this->managers;

        if ($name instanceof Request) {
            if (!$path) {
                $path = $name->query->get('path');
            }

            $name = $name->query->get('filesystem');
        }

        if (array_key_exists($name, $managers)) {
            $config = $this->managers[$name];
        } else {
            // return 1st by default
            $config = array_shift($managers);
            $names = array_keys($this->managers);
            $name = array_shift($names);
        }

        $filesystem = new $config['filesystem_class']($path, $config['root_dir'], $config['web_prefix']);

        return new $config['filesystem_manager_class']($name, $filesystem, $this->alerts, $this->permissions);
    }

    public function getManagerNames()
    {
        return array_keys($this->managers);
    }
}