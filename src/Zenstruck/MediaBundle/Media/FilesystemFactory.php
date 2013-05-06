<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zenstruck\MediaBundle\Media\Alert\AlertProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactory
{
    protected $alertProvider;
    protected $managers = array();

    public function __construct(AlertProviderInterface $alertProvider)
    {
        $this->alertProvider = $alertProvider;
    }

    public function addManager($name, array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array('root_dir', 'web_prefix'));

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

        $filesystem = new Filesystem($path, $config['root_dir'], $config['web_prefix']);

        return new FilesystemManager($name, $filesystem, $this->alertProvider);
    }

    public function getManagerNames()
    {
        return array_keys($this->managers);
    }
}