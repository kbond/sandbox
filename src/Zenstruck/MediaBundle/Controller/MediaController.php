<?php

namespace Zenstruck\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;

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
    public function deleteFileAction($filename, Request $request)
    {
        $manager = $this->getManager();
        $manager->deleteFile($request->query->get('path'), $filename);

        return $this->redirectToPath($manager->getPath());
    }

    /**
     * @Route("/mkdir", name="zenstruck_media_mkdir")
     * @Method("POST")
     */
    public function createDirectoryAction(Request $request)
    {
        $manager = $this->getManager();
        $manager->mkDir($request->query->get('path'), $request->request->get('dir_name'));

        return $this->redirectToPath($manager->getPath());
    }

    protected function redirectToPath($path)
    {
        return $this->redirect($this->generateUrl('zenstruck_media_list', array('path' => $path)));
    }

    /**
     * @return \Zenstruck\MediaBundle\Media\FilesystemManager
     */
    protected function getManager()
    {
        return $this->get('zenstruck_media.filesystem_manager');
    }
}