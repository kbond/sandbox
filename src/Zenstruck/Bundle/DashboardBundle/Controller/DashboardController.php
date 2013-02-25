<?php

namespace Zenstruck\Bundle\DashboardBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Zenstruck\Bundle\DashboardBundle\Dashboard\DashboardManager;

class DashboardController
{
    protected $templating;
    protected $dashboardManager;

    public function __construct(EngineInterface $templating, DashboardManager $dashboardManager)
    {
        $this->templating = $templating;
        $this->dashboardManager = $dashboardManager;
    }

    public function dashboardAction()
    {
        $content = $this->templating->render('ZenstruckDashboardBundle:Twitter:dashboard.html.twig', array(
                'layout' => $this->dashboardManager->getLayout()
            ));

        return new Response($content);
    }

    public function menuAction()
    {
        $content = $this->templating->render('ZenstruckDashboardBundle:Twitter:_menu.html.twig', array(
                'manager' => $this->dashboardManager
            ));

        return new Response($content);
    }
}
