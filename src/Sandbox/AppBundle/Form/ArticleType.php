<?php

namespace Sandbox\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('date', 'date', array('format' => 'MMM-dd-yyyy'))
            ->add('editor', 'zenstruck_tunnel_entity', array(
                    'class' => 'AppBundle:Author',
                    'required' => false,
                    'callback' => 'MyApp.select2Callback'
                ))
            ->add('author', 'zenstruck_ajax_entity', array(
                    'class' => 'AppBundle:Author',
                    'property' => 'name',
                    'use_controller' => true
                ))
            ->add('tags', 'zenstruck_ajax_entity', array(
                    'class' => 'AppBundle:Tag',
                    'property' => 'name',
                    'use_controller' => true,
                    'multiple' => true
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sandbox\AppBundle\Entity\Article'
        ));
    }

    public function getName()
    {
        return 'sandbox_appbundle_articletype';
    }
}
