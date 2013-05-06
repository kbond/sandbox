<?php

namespace Zenstruck\MediaBundle\Media;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;
use Zenstruck\MediaBundle\Exception\Exception;
use Zenstruck\MediaBundle\Media\Alert\AlertProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManager
{
    protected $rootDir;
    protected $webPrefix;
    protected $filesystem;
    protected $alert;
    protected $configured = false;
    protected $directories;
    protected $workingDir;
    protected $files;
    protected $path;

    public function __construct($rootDir, $webPrefix, AlertProviderInterface $alert)
    {
        $this->filesystem = new Filesystem();
        $this->webPrefix = trim($webPrefix) === '/' ? '/' : sprintf('/%s/', trim($webPrefix, '/'));
        $this->rootDir = rtrim($rootDir, '\\/');
        $this->alert = $alert;
    }

    public function configure($path = null)
    {
        $this->path = trim($path, '/');
        $this->workingDir = rtrim($this->rootDir.'/'.$this->path, '/').'/';

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

    public function getWorkingDir()
    {
        $this->ensureConfigured();

        return $this->workingDir;
    }

    public function getFiles()
    {
        $this->ensureConfigured();

        if ($this->files) {
            return $this->files;
        }

        $files = Finder::create()
            ->sort(function(\SplFileInfo $a, \SplFileInfo $b) {
                    if ($a->isDir() && $b->isFile()) {
                        return -1;
                    } elseif ($a->isFile() && $b->isDir()) {
                        return 1;
                    }

                    return strcasecmp($a->getFilename(), $b->getFilename());
                })
            ->depth(0)
            ->in($this->workingDir)
        ;

        foreach ($files as $file) {
            $this->files[] = new File($file, $this->webPrefix.$this->path);
        }

        return $this->files;
    }

    public function getAncestry()
    {
        $this->ensureConfigured();

        if ($this->path) {
            return explode('/', $this->path);
        }

        return array();
    }

    public function getPreviousPath()
    {
        return join('/', array_slice($this->getAncestry(), 0, count($this->getAncestry()) - 1));
    }

    public function renameFile($path, $oldName, $newName)
    {
        $this->configure($path);

        $oldFile = $this->workingDir.$oldName;
        $type = is_dir($oldFile) ? 'directory' : 'file';

        if ('file' === $type) {
            // don't let user change extension
            $newName = pathinfo($newName, PATHINFO_FILENAME).'.'.pathinfo($oldName, PATHINFO_EXTENSION);
        }

        if ($newName === $oldName) {
            $this->addAlert(sprintf('You didn\'t specify a new name for "%s".', $newName), 'error');
            return;
        }

        $newFile = $this->workingDir.$newName;

        if (file_exists($newFile)) {
            $this->addAlert(sprintf('The %s "%s" already exists.', $type, $newName), 'error');
            return;
        }

        try {
            $this->filesystem->rename($oldFile, $newFile);
        } catch (\Exception $e) {
            $this->addAlert(sprintf('Error renaming %s "%s".  Check permissions.', $type, $oldName), 'error');
            return;
        }

        $this->addAlert(sprintf('%s "%s" renamed to "%s".', ucfirst($type), $oldName, $newName));
    }

    public function deleteFile($path, $filename)
    {
        $this->configure($path);

        $file = $this->workingDir.$filename;
        $type = is_dir($file) ? 'directory' : 'file';

        try {
            $this->filesystem->remove($file);
        } catch (\Exception $e) {
            $this->addAlert(sprintf('Error deleting %s "%s".  Check permissions.', $type, $filename), 'error');
            return;
        }

        $this->addAlert(sprintf('%s "%s" deleted.', ucfirst($type), $filename));
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

    protected function addAlert($message, $type = 'success')
    {
        $this->alert->addAlert($message, $type);
    }

    protected function ensureConfigured()
    {
        if (!$this->configured) {
            throw new Exception('Filesystem manager not configured with Filesystem::configure()');
        }
    }
}