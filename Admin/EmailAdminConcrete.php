<?php

namespace Librinfo\EmailBundle\Admin;

use Html2Text\Html2Text;
use Blast\CoreBundle\Admin\Traits\HandlesRelationsAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class EmailAdminConcrete extends EmailAdmin
{
    use HandlesRelationsAdmin { configureFormFields as configFormHandlesRelations; }
    
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('send', $this->getRouterIdParameter().'/send');
    }

    protected function configureFormFields(FormMapper $mapper)
    {
        $this->configFormHandlesRelations($mapper);

        $builder = $mapper->getFormBuilder();
        $factory = $builder->getFormFactory();
        $request = $this->getRequest();
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($request, $factory) {
            $form = $event->getForm();
            if (!empty($request->get('force_user'))) {
                $options = $form->get('field_from')->getConfig()->getOptions();
                $options['auto_initialize'] = false;
                $options['attr']['readonly'] = 'readonly';
                $form->remove('field_from');
                $form->add($factory->createNamed('field_from', 'text', null, $options));
            }
        });
    }

    /**
     * @param FormMapper $mapper
     */
    public function postConfigureFormFields(FormMapper $mapper)
    {

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
