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
            ;
    }

    public function prePersist($email){

        Parent::prePersist($email);
        
        foreach ($email->getAttachments() as $attachment) {
            $this->getAttachmentAdmin()->prePersist($attachment);
        }
        
        $email->setTemplate(NULL);

        $this->setText($email);
    }

    public function preUpdate($email){

        Parent::preUpdate($email);
        
        foreach ($email->getAttachments() as $attachment) {
            $this->getAttachmentAdmin()->preUpdate($attachment);
        }
        
         $email->setTemplate(NULL);

        $this->setText($email);
    }
    
        public function preRemove($email)
    {
        Parent::preRemove($email);
            
        foreach ($email->getAttachments($email) as $attachment) {
            $this->getAttachmentAdmin()->preRemove($attachment);
        }
    }

    protected function setText($email){

        $html2T = new Html2Text($email->getContent());
        $textContent= $html2T->getText();
        $email->setTextContent($textContent);
    }
    
    private function getAttachmentAdmin(){
        return $this
            ->getConfigurationPool()
            ->getAdminByAdminCode('email.email_attachment');
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
