<?php

namespace Librinfo\EmailBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
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
        $textContent = $html2T->getText();
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
                        ),
                        'duplicate' => array(
                            'template' => 'LibrinfoEmailBundle:CRUD:list__action_duplicate.html.twig'
                        )
                    )
                ))
        ;
    }

}
