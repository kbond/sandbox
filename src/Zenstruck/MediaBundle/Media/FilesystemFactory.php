<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactory
{
    protected $managers = array();

    public function addManager($name, FilesystemManager $manager)
    {
        $this->managers[$name] = $manager;
    }

    /**
     * @param Request|string|null $name
     *
     * @return FilesystemManager
     */
    public function getManager($name = null)
    {
        $managers = $this->managers;

        if ($name instanceof Request) {
            $name = $name->query->get('filesystem');
        }

        if (!array_key_exists($name, $managers)) {
            // return 1st by default
            return array_shift($managers);
        }

        return $managers[$name];
    }

    public function getManagerNames()
    {
        return array_keys($this->managers);
    }
}