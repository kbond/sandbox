<?php

namespace Zenstruck\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zenstruck\MediaBundle\Exception\DirectoryNotFoundException;
use Zenstruck\MediaBundle\Media\FilesystemFactory;
use Zenstruck\MediaBundle\Media\FilesystemManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MediaController
{
    protected $factory;
    protected $templating;
    protected $router;

    public function __construct(FilesystemFactory $factory, EngineInterface $templating, UrlGeneratorInterface $router)
    {
        $this->factory = $factory;
        $this->templating = $templating;
        $this->router = $router;
    }

    public function listAction(Request $request)
    {
        $manager = $this->factory->getManager($request);

        try {
            $manager->configure($request->query->get('path'));
        } catch (DirectoryNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new Response($this->templating->render('ZenstruckMediaBundle:Twitter:list.html.twig', array(
                'manager' => $manager,
                'filesystems' => $this->factory->getManagerNames()
            )));
    }

    public function uploadAction(Request $request)
    {
        $manager = $this->factory->getManager($request);
        $manager->uploadFile($request->query->get('path'), $request->files->get('file'));

        return $this->redirect($manager);
    }

    public function deleteAction($filename, Request $request)
    {
        $manager = $this->factory->getManager($request);
        $manager->deleteFile($request->query->get('path'), $filename);

        return $this->redirect($manager);
    }

    public function renameAction($filename, Request $request)
    {
        $manager = $this->factory->getManager($request);
        $manager->renameFile($request->query->get('path'), $filename, $request->request->get('new_name'));

        return $this->redirect($manager);
    }

    public function createDirectoryAction(Request $request)
    {
        $manager = $this->factory->getManager($request);
        $manager->mkDir($request->query->get('path'), $request->request->get('dir_name'));

        return $this->redirect($manager);
    }

    protected function redirect(FilesystemManager $manager)
    {
        return new RedirectResponse(
            $this->router->generate('zenstruck_media_list', $manager->getRequestParams())
        );
    }
}