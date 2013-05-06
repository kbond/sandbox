<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactory
{
    /** @var FilesystemManager[] */
    protected $managers = array();

    public function addManager($name, FilesystemManager $manager)
    {
        $this->managers[$name] = $manager;
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

        if (!array_key_exists($name, $managers)) {
            // return 1st by default
            $manager = array_shift($managers);
            return $manager->prepare($path);
        }

        return $managers[$name]->prepare($path);
    }

    public function getManagerNames()
    {
        return array_keys($this->managers);
    }
}