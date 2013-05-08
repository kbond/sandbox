<?php

namespace Zenstruck\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class MediaType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'disabled' => true
            ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'zenstruck_media';
    }
}