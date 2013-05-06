<?php

namespace Zenstruck\MediaBundle\Media;

use Zenstruck\MediaBundle\Exception\Exception;
use Zenstruck\MediaBundle\Media\Alert\AlertProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManager
{
    const ALERT_ERROR   = 'error';
    const ALERT_SUCCESS = 'success';

    protected $name;
    protected $rootDir;
    protected $webPrefix;
    protected $alertProvider;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct($name, Filesystem $filesystem, AlertProviderInterface $alertProvider)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
        $this->alertProvider = $alertProvider;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFiles()
    {
        return $this->filesystem->getFiles();
    }

    public function getPath()
    {
        return $this->filesystem->getPath();
    }

    public function getRequestParams(array $params = array())
    {
        $defaults = array(
            'path' => $this->getPath(),
            'filesystem' => $this->name
        );

        return array_merge($defaults, $params);
    }

    public function getAncestry()
    {
        if ($this->getPath()) {
            return explode('/', $this->getPath());
        }

        return array();
    }

    public function getPreviousPath()
    {
        return join('/', array_slice($this->getAncestry(), 0, count($this->getAncestry()) - 1));
    }

    public function renameFile($oldName, $newName)
    {
        try {
            $this->filesystem->renameFile($oldName, $newName);
        } catch (Exception $e) {
            $this->alertProvider->addAlert($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alertProvider->addAlert(sprintf('%s renamed to "%s".', $oldName, $newName), static::ALERT_ERROR);
    }

    public function deleteFile($filename)
    {
        try {
            $this->filesystem->deleteFile($filename);
        } catch (Exception $e) {
            $this->alertProvider->addAlert($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alertProvider->addAlert(sprintf('"%s" deleted.', $filename), static::ALERT_SUCCESS);
    }

    public function mkDir($dirName)
    {
        try {
            $this->filesystem->mkDir($dirName);
        } catch (Exception $e) {
            $this->alertProvider->addAlert($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alertProvider->addAlert(sprintf('Directory "%s" created.', $dirName), static::ALERT_SUCCESS);
    }

    public function uploadFile($file)
    {
        try {
            $filename = $this->filesystem->uploadFile($file);
        } catch (Exception $e) {
            $this->alertProvider->addAlert($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alertProvider->addAlert(sprintf('File "%s" uploaded.', $filename), static::ALERT_SUCCESS);
    }
}