<?php

namespace Librinfo\EmailBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Librinfo\CoreBundle\Admin\Traits\HandlesRelationsAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Html2Text\Html2Text;

class EmailAdminConcrete extends EmailAdmin
{
    use HandlesRelationsAdmin;
    
    public $supportsPreviewMode = true;

    protected function configureRoutes(RouteCollection $collection)
    {
    $collection->add('send', $this->getRouterIdParameter().'/send')
               ->add('getTemplate', $this->getRouterIdParameter().'/getTemplate')
            ;
    }

    public function prePersist($email){

        Parent::prePersist($email);

        $this->setText($email);
    }

    public function preUpdate($email){

        Parent::preUpdate($email);

        $this->setText($email);
    }

    protected function setText($email){

        $html2T = new Html2Text($email->getContent());
        $textContent= $html2T->getText();
        $email->setTextContent($textContent);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('field_to')
            ->add('field_subject')
            ->add('sent')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                    'send' => array(
                    'template' => 'LibrinfoEmailBundle:CRUD:list__action_send.html.twig'
                    )
                )
            ))
        ;
    }
}
