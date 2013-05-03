<?php

namespace Zenstruck\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;
use Zenstruck\MediaBundle\Exception\FileExistsException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MediaController extends Controller
{
    /**
     * @Route("/", name="zenstruck_media_list")
     */
    public function listAction(Request $request)
    {
        $manager = $this->getManager();

        try {
            $manager->configure($request->query->get('path'));
        } catch (DirectoryNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('ZenstruckMediaBundle:Twitter:list.html.twig', array(
                'manager' => $manager
            ));
    }

    /**
     * @Route("/upload", name="zenstruck_media_upload")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        $manager = $this->getManager();
        $manager->uploadFile($request->query->get('path'), $request->files->get('file'));

        return $this->redirectToPath($manager->getPath());
    }

    /**
     * @Route("/delete_file/{filename}", name="zenstruck_media_delete_file")
     * @Method("DELETE")
     */
    public function deleteFileAction($filename)
    {
        $path = $this->getPath();
        $workingDir = $this->getWorkingDirectory($path);
        $file = $workingDir.$filename;

        if (!is_file($file)) {
            return $this->redirectToPath($path, sprintf('Could not delete "%s". Not a valid file.', $filename), 'error');
        }

        $filesystem = new Filesystem();

        try {
            $filesystem->remove($file);
        } catch (\Exception $e) {
            return $this->redirectToPath($path, sprintf('Error deleting file "%s".  Check permissions.', $filename), 'error');
        }

        return $this->redirectToPath($path, sprintf('File "%s" deleted.', $filename));
    }

    /**
     * @Route("/mkdir", name="zenstruck_media_mkdir")
     * @Method("POST")
     */
    public function createDirectoryAction(Request $request)
    {
        $path = $this->getPath();
        $workingDir = $this->getWorkingDirectory($path);
        $dirName = $request->request->get('dir_name');

        if (!$dirName) {
            return $this->redirectToPath($path, 'You entered an empty directory name.', 'error');
        }

        $filesystem = new Filesystem();
        $newDir = $workingDir.$dirName;

        if ($filesystem->exists($newDir)) {
            return $this->redirectToPath($path, sprintf('Failed to create directory "%s".  It already exists.', $dirName), 'error');
        }

        try {
            $filesystem->mkdir($newDir);
        } catch (\Exception $e) {
            return $this->redirectToPath($path, sprintf('Error creating directory "%s".  Check permissions.', $dirName), 'error');
        }

        return $this->redirectToPath($path, sprintf('Directory "%s" created.', $dirName));
    }

    protected function redirectToPath($path, $flashMessage = null, $flashType = 'success')
    {
        if ($flashMessage) {
            $this->getManager()->addAlert($flashMessage, $flashType);
        }

        return $this->redirect($this->generateUrl('zenstruck_media_list', array('path' => $path)));
    }

    protected function getPath()
    {
        return trim($this->getRequest()->query->get('path'), '/');
    }

    protected function getWorkingDirectory($path)
    {
        return rtrim($this->container->getParameter('kernel.root_dir').'/../web/files/'.$path, '/').'/';
    }

    /**
     * @return \Zenstruck\MediaBundle\Media\FilesystemManager
     */
    protected function getManager()
    {
        return $this->get('zenstruck_media.filesystem_manager');
    }
}