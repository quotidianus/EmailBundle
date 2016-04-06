<?php

namespace Librinfo\EmailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LibrinfoEmailBundle:Default:index.html.twig', array('name' => $name));
    }
}
