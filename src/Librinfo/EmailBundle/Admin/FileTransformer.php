<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FileTransformer implements DataTransformerInterface
{
    public function reverseTransform($file)
    {
        return base64_encode($file);
    }

    public function transform($file)
    {
        return base64_decode($file);
    }

}
