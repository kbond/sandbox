<?php

namespace Sandbox\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class WidgetController extends Controller
{
    /**
     * @Template
     */
    public function configAction()
    {
        $file = $this->container->getParameter('kernel.root_dir').'/config/dashboard.yml';

        return array('config' => file_get_contents($file));
    }

    /**
     * @Route("/widget_esi", name="widget_esi")
     * @Cache(smaxage="10")
     * @Template
     */
    public function esiAction()
    {
        return array();
    }

    /**
     * @Route("/widget_hinclude", name="widget_hinclude")
     * @Template
     */
    public function hincludeAction()
    {
        return array();
    }

    /**
     * @Route("/widget_ajax", name="widget_ajax")
     * @Template
     */
    public function ajaxAction()
    {
        sleep(1);

        return array();
    }
}
