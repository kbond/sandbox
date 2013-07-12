<?php

namespace Sandbox\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\ResourceBundle\Config\Resource;
use Zenstruck\ResourceBundle\Controller\ResourceController;
use Zenstruck\Bundle\FormBundle\Form\GroupedFormView;

class ArticleController extends ResourceController
{
    public function editRandomAction()
    {
        $articles = $this->getCollection();

        shuffle($articles);

        if (isset($articles[0])) {
            return $this->util->redirect($this->util->generateUrl('edit_article', array('id' => $articles[0]->getId())));
        }

        return $this->util->redirect($this->util->generateUrl('new_article'));
    }

    protected function renderResponse($action, $data = array())
    {
        if (Resource::ACTION_LIST !== $action) {
            $this->addBreadcrumb();
        }

        if (isset($data['form'])) {
            $data['grouped_form'] = new GroupedFormView($data['form']);
            unset($data['form']);
        }

        return parent::renderResponse($action, $data);
    }

    protected function addBreadcrumb()
    {
        $this->util->get('zenstruck_dashboard.manager')->addBreadcrumb('Articles', $this->util->generateUrl('list_articles'));
    }
}
