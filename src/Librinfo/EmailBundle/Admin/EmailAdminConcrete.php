<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\Traits\HandlesRelationsAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Html2Text\Html2Text;

class EmailAdminConcrete extends EmailAdmin
{

    use HandlesRelationsAdmin;

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('send', $this->getRouterIdParameter() . '/send');
        $collection->add('duplicate', $this->getRouterIdParameter() . '/duplicate');
    }

    public function prePersist($email)
    {

        parent::prePersist($email);

        $email->setTemplate(NULL);

        $this->setText($email);
    }

    public function preUpdate($email)
    {

        parent::preUpdate($email);

        $email->setTemplate(NULL);

        $this->setText($email);
    }

    protected function setText($email)
    {

        $html2T = new Html2Text($email->getContent());
     
        $email->setTextContent($html2T->getText());
    }

}
