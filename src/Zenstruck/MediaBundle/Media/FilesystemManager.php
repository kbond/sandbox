<?php

namespace Zenstruck\MediaBundle\Media;

use Zenstruck\MediaBundle\Exception\Exception;
use Zenstruck\MediaBundle\Media\Alert\AlertProviderInterface;
use Zenstruck\MediaBundle\Media\Permission\PermissionProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManager
{
    const ALERT_ERROR   = 'error';
    const ALERT_SUCCESS = 'success';

    const VIEW_DETAIL   = 'detail';
    const VIEW_THUMB    = 'thumb';

    const LAYOUT_IFRAME = 'iframe';
    const LAYOUT_DEFAULT= null;

    protected $name;
    protected $view;
    protected $layout;

    protected $alerts;
    protected $permissions;

    /** @var Filesystem */
    protected $filesystem;
    protected $files;

    public function __construct($name, $view, $layout, Filesystem $filesystem, AlertProviderInterface $alerts, PermissionProviderInterface $permissions)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
        $this->alerts = $alerts;
        $this->permissions = $permissions;

        if (!in_array($view, static::getAvailableViews())) {
            $view = static::getDefaultView();
        }

        $this->view = $view;

        if (!in_array($layout, static::getAvailableLayouts())) {
            $layout = static::getDefaultLayout();
        }

        $this->layout = $layout;

        $this->files = $this->filesystem->getFiles();

        if (!$filesystem->isWritable()) {
            $this->alerts->add('This directory is not writable.  Check permissions.', static::ALERT_ERROR);
        }
    }

    public static function getAvailableViews()
    {
        return array(static::VIEW_THUMB, static::VIEW_DETAIL);
    }

    public static function getDefaultView()
    {
        return static::VIEW_THUMB;
    }

    public static function getAvailableLayouts()
    {
        return array(static::LAYOUT_IFRAME, static::LAYOUT_DEFAULT);
    }

    public static function getDefaultLayout()
    {
        return static::LAYOUT_DEFAULT;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getPath()
    {
        return $this->filesystem->getPath();
    }

    public function getRequestParams(array $params = array())
    {
        $defaults = array(
            'path' => $this->getPath(),
            'filesystem' => $this->name,
            'view' => $this->view,
            'layout' => $this->layout
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
        if (!$this->permissions->canRenameFile()) {
            $this->alerts->add('You do not have the required permission to rename files.', static::ALERT_ERROR);
            return;
        }

        try {
            $this->filesystem->renameFile($oldName, $newName);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('File "%s" renamed to "%s".', $oldName, $newName), static::ALERT_SUCCESS);
    }

    public function renameDir($oldName, $newName)
    {
        if (!$this->permissions->canRenameDir()) {
            $this->alerts->add('You do not have the required permission to rename directories.', static::ALERT_ERROR);
            return;
        }

        try {
            $this->filesystem->renameFile($oldName, $newName);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('Directory "%s" renamed to "%s".', $oldName, $newName), static::ALERT_SUCCESS);
    }

    public function deleteFile($filename)
    {
        if (!$this->permissions->canDeleteFile()) {
            $this->alerts->add('You do not have the required permission to delete files.', static::ALERT_ERROR);
            return;
        }

        try {
            $this->filesystem->deleteFile($filename);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('File "%s" deleted.', $filename), static::ALERT_SUCCESS);
    }

    public function deleteDir($filename)
    {
        if (!$this->permissions->canDeleteDir()) {
            $this->alerts->add('You do not have the required permission to delete directories.', static::ALERT_ERROR);
            return;
        }

        try {
            $this->filesystem->deleteFile($filename);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('Directory "%s" deleted.', $filename), static::ALERT_SUCCESS);
    }

    public function mkDir($dirName)
    {
        if (!$this->permissions->canMkDir()) {
            $this->alerts->add('You do not have the required permission to create directories.', static::ALERT_ERROR);
            return;
        }

        try {
            $this->filesystem->mkDir($dirName);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('Directory "%s" created.', $dirName), static::ALERT_SUCCESS);
    }

    public function uploadFile($file)
    {
        if (!$this->permissions->canUploadFile()) {
            $this->alerts->add('You do not have the required permission to upload files.', static::ALERT_ERROR);
            return;
        }

        try {
            $filename = $this->filesystem->uploadFile($file);
        } catch (Exception $e) {
            $this->alerts->add($e->getMessage(), static::ALERT_ERROR);
            return;
        }

        $this->alerts->add(sprintf('File "%s" uploaded.', $filename), static::ALERT_SUCCESS);
    }
}