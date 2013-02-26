<?php

namespace Sandbox\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AdminController extends Controller
{
    /**
     * @Template
     */
    public function configAction()
    {
        $file = $this->container->getParameter('kernel.root_dir').'/config/dashboard.yml';

        return array('config' => file_get_contents($file));
    }
}