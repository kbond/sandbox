<?php

namespace Sandbox\AppBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FixturesLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('sandbox:fixtures:load')
            ->setDescription('Load fixtures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $loader = new \Nelmio\Alice\Loader\Yaml();
        $persister = new \Nelmio\Alice\ORM\Doctrine($em);

        $em->createQuery('DELETE AppBundle:Article')->execute();
        $em->createQuery('DELETE AppBundle:Tag')->execute();
        $em->createQuery('DELETE AppBundle:Author')->execute();

        $file = $this->getContainer()->getParameter('kernel.root_dir').'/config/fixtures.yml';
        $objects = $loader->load($file);
        $persister->persist($objects);
    }
}