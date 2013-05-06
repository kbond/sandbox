<?php

namespace Zenstruck\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;
use Zenstruck\MediaBundle\Media\FilesystemManager;

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
        $factory = $this->get('zenstruck_media.filesystem_factory');
        $manager = $factory->getManager($request);

        try {
            $manager->configure($request->query->get('path'));
        } catch (DirectoryNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('ZenstruckMediaBundle:Twitter:list.html.twig', array(
                'manager' => $manager,
                'filesystems' => $factory->getManagerNames()
            ));
    }

    /**
     * @Route("/upload", name="zenstruck_media_upload")
     * @Method("POST")
     */
    public function uploadAction(Request $request)
    {
        $manager = $this->getManager($request);
        $manager->uploadFile($request->query->get('path'), $request->files->get('file'));

        return $this->redirectToPath($manager);
    }

    /**
     * @Route("/delete/{filename}", name="zenstruck_media_delete")
     * @Method("DELETE")
     */
    public function deleteAction($filename, Request $request)
    {
        $manager = $this->getManager($request);
        $manager->deleteFile($request->query->get('path'), $filename);

        return $this->redirectToPath($manager);
    }

    /**
     * @Route("/rename/{filename}", name="zenstruck_media_rename")
     * @Method("POST")
     */
    public function renameAction($filename, Request $request)
    {
        $manager = $this->getManager($request);
        $manager->renameFile($request->query->get('path'), $filename, $request->request->get('new_name'));

        return $this->redirectToPath($manager);
    }

    /**
     * @Route("/mkdir", name="zenstruck_media_mkdir")
     * @Method("POST")
     */
    public function createDirectoryAction(Request $request)
    {
        $manager = $this->getManager($request);
        $manager->mkDir($request->query->get('path'), $request->request->get('dir_name'));

        return $this->redirectToPath($manager);
    }

    protected function redirectToPath(FilesystemManager $manager)
    {
        return $this->redirect($this->generateUrl('zenstruck_media_list', $manager->getRequestParams()));
    }

    /**
     * @return \Zenstruck\MediaBundle\Media\FilesystemManager
     */
    protected function getManager(Request $request)
    {
        return $this->get('zenstruck_media.filesystem_factory')->getManager($request);
    }
}