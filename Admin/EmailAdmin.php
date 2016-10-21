<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\CoreAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class EmailAdmin extends CoreAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('send');
        $collection->add('duplicate');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        if ($this->hasRequest()) {
            $force_user = $this->getRequest()->get('force_user');
            if ($force_user) {
                $user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
                if ($user)
                    $object->setFieldFrom($user->getEmail());
            }

            $recipients = $this->getRequest()->get('recipients', []);
            if (!is_array($recipients))
                $recipients = [$recipients];

            $recipient_class = $this->getRequest()->get('recipient_class');
            $recipient_ids = $this->getRequest()->get('recipient_ids');
            if ($recipient_ids && $recipient_class) {
                $ids = is_array($recipient_ids) ? $recipient_ids : [$recipient_ids];
                $entities = $this->getModelManager()->findBy($recipient_class, ['id' => $ids]);
                $rc = new \ReflectionClass($recipient_class);
                $adder = 'add' . $rc->getShortName();
                foreach ($entities as $entity){
                    $object->$adder($entity);
                    if ($entity->getEmail())
                        $recipients[] = $entity->getEmail();
                }
            }

            $object->setFieldTo(implode(', ', array_unique($recipients)));
        }

        return $object;
    }

    public function getPersistentParameters()
    {
        $parentParams = parent::getPersistentParameters();

        if (!$this->getRequest()) {
            return $parentParams;
        }

        $params = [];

        if ($from_admin = $this->getRequest()->get('from_admin'))
            $params['from_admin'] = $from_admin;
        if ($from_id = $this->getRequest()->get('from_id'))
            $params['from_id'] = $from_id;

        return array_merge($parentParams, $params);
    }

}
