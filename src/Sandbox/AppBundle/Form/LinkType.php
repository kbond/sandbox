<?php

namespace Sandbox\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url')
            ->add('author', 'zenstruck_ajax_entity', array(
                    'class' => 'AppBundle:Author',
                    'property' => 'name',
                    'placeholder' => 'Link Author',
                    'help' => 'Try typing a letter such as "k"',
                    'use_controller' => true
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sandbox\AppBundle\Entity\Link'
        ));
    }

    public function getName()
    {
        return 'sandbox_appbundle_linktype';
    }
}
