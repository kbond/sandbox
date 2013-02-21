<?php

namespace Sandbox\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/message/{message}", name="message")
     * @Template()
     */
    public function messageAction($message)
    {
        $this->get('old_sound_rabbit_mq.hello_producer')->publish($message);

        return new Response(sprintf('Message "%s" sent.', $message));
    }
}