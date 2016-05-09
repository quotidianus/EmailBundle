<?php

namespace Librinfo\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChartistType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'chartist';
    }

}
