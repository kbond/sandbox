<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;
use Zenstruck\MediaBundle\Exception\Exception;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManager
{
    protected $rootDir;
    protected $filesystem;
    protected $router;
    protected $session;
    protected $configured = false;
    protected $directories;
    protected $workingDir;
    protected $files;
    protected $path;

    public function __construct($rootDir, RouterInterface $router, Session $session)
    {
        $this->filesystem = new Filesystem();
        $this->rootDir = rtrim($rootDir, '\\/');
        $this->router = $router;
        $this->session = $session;
    }

    public function configure($path)
    {
        $this->path = $path;
        $this->workingDir = $this->getWorkingDirectory($path);

        if (!is_dir($this->workingDir)) {
            throw new DirectoryNotFoundException(sprintf('Directory "%s" not found.', $this->workingDir));
        }

        $this->configured = true;
    }

    public function getPath()
    {
        $this->ensureConfigured();

        return $this->path;
    }

    public function getFiles()
    {
        $this->ensureConfigured();

        if ($this->files) {
            return $this->files;
        }

        return $this->files = Finder::create()
            ->sort(function(\SplFileInfo $a, \SplFileInfo $b) {
                    return strcasecmp($a->getFilename(), $b->getFilename());
                })
            ->files()
            ->depth(0)
            ->in($this->workingDir)
        ;
    }

    public function getDirectories()
    {
        $this->ensureConfigured();

        if ($this->directories) {
            return $this->directories;
        }

        return $this->directories = Finder::create()
            ->sortByName()
            ->directories()
            ->depth(0)
            ->in($this->workingDir)
        ;
    }

    public function getAncestry()
    {
        $this->ensureConfigured();

        return explode('/', $this->path);
    }

    public function deleteFile($path, $filename)
    {
        $this->configure($path);

        $file = $this->workingDir.$filename;

        if (!is_file($file)) {
            $this->addAlert(sprintf('Could not delete "%s". Not a valid file.', $filename), 'error');
            return;
        }

        try {
            $this->filesystem->remove($file);
        } catch (\Exception $e) {
            $this->addAlert(sprintf('Error deleting file "%s".  Check permissions.', $filename), 'error');
            return;
        }

        $this->addAlert(sprintf('File "%s" deleted.', $filename));
    }

    public function mkDir($path, $dirName)
    {
        $this->configure($path);

        if (!$dirName) {
            $this->addAlert('You didn\'t enter a directory name.', 'error');
            return;
        }

        $newDir = $this->workingDir.$dirName;

        if ($this->filesystem->exists($newDir)) {
            $this->addAlert(sprintf('Failed to create directory "%s".  It already exists.', $dirName), 'error');
            return;
        }

        try {
            $this->filesystem->mkdir($newDir);
        } catch (\Exception $e) {
            $this->addAlert(sprintf('Error creating directory "%s".  Check permissions.', $dirName), 'error');
            return;
        }

        $this->addAlert(sprintf('Directory "%s" created.', $dirName));
    }

    public function uploadFile($path, $file)
    {
        $this->configure($path);

        if (!$file instanceof UploadedFile) {
            $this->addAlert('No file selected.', 'error');
            return;
        }

        if (file_exists($this->workingDir.$file->getClientOriginalName())) {
            $this->addAlert(sprintf('File "%s" already exists.', $file->getClientOriginalName()), 'error');
            return;
        }

        $filename = $file->getClientOriginalName();

        try {
            $file->move($this->workingDir, $filename);
        } catch (\Exception $e) {
            $this->addAlert(sprintf('Error uploading file "%s".  Check permissions.', $filename));
            return;
        }

        $this->addAlert(sprintf('File "%s" uploaded.', $filename));
    }

    public function addAlert($message, $type = 'success')
    {
        $this->session->getFlashBag()->add($type, $message);
    }

    protected function ensureConfigured()
    {
        if (!$this->configured) {
            throw new Exception('Filesystem manager not configured with Filesystem::configure()');
        }
    }

    protected function getWorkingDirectory($path)
    {
        return $this->rootDir.'/'.trim($path, '/');
    }
}