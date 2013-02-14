<?php

namespace Sandbox\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sandbox\AppBundle\Entity\Article;
use Sandbox\AppBundle\Form\ArticleType;

/**
 * Article controller.
 *
 * @Route("/author")
 */
class AuthorController extends Controller
{
    /**
     * @Route("/find", name="author_find")
     */
    public function findAction()
    {
        $query = $this->getRequest()->query->get('q');

        $qb = $this->getDoctrine()->getRepository('AppBundle:Author')->createQueryBuilder('author');

        $qb->where('author.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
        ;

        $results = $qb->getQuery()->execute();

        $ret = array();

        foreach ($results as $result) {
            $ret[] = array(
                'id' => $result->getId(),
                'text' => (string) $result
            );
        }

        return new JsonResponse($ret);
    }
}