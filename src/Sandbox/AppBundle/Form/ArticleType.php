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
            ->add('cost', 'money', array(
                    'currency' => 'USD'
                ))
            ->add('body', null, array(
                    'attr' => array('class' => 'wysiwyg')
                ))
            ->add('media', 'zenstruck_media', array(
                    'required' => false,
                    'filesystem' => 'Images'
                ))
            ->add('date', 'date', array(
                    'format' => 'MMM-dd-yyyy',
                    'group' => 'Date',
                ))
            ->add('editor', 'zenstruck_tunnel_entity', array(
                    'class' => 'AppBundle:Author',
                    'required' => false,
                    'help' => 'Clear button works out of the box but Select button callback needs to be implemented by you',
                    'callback' => 'MyApp.select2Callback'
                ))
            ->add('author', 'zenstruck_ajax_entity', array(
                    'class' => 'AppBundle:Author',
                    'property' => 'name',
                    'placeholder' => 'Choose and Author',
                    'help' => 'Try typing a letter such as "k"',
                    'use_controller' => true
                ))
            ->add('tags', 'zenstruck_ajax_entity', array(
                    'class' => 'AppBundle:Tag',
                    'property' => 'name',
                    'use_controller' => true,
                    'placeholder' => 'Choose Tags',
                    'help' => 'Try typing a letter such as "o"',
                    'multiple' => true
                ))
            ->add('links', 'collection', array(
                    'type' => new LinkType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ));
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
