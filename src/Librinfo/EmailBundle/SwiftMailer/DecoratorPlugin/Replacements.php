<?php

namespace Librinfo\EmailBundle\SwiftMailer\DecoratorPlugin;

use Symfony\Component\DependencyInjection\ContainerAware;

class Replacements extends ContainerAware implements Swift_Plugins_Decorator_Replacements
{

  public function getReplacementsFor($address)
  {
    if (class_exists('Librinfo\CRMBundle\LibrinfoCRMBundle')){

        $em = $this->container->get('doctrine')->getManager();

        $contact = $em->findOneBy(array("email" => $adress));

        if ($contact) {
          return array(
            '{prenom}' => $contact->getFirstName(),
            '{nom}' => $contact->getName(),
            '{titre}' => $contact->getTitle()
          );
        }
    }
  }
}
