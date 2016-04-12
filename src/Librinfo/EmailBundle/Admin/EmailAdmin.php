<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\CoreAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmailAdmin extends CoreAdmin
{
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
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    // protected function configureListFields(ListMapper $listMapper)
    // {
    //     $listMapper
    //         ->add('field_from')
    //         ->add('field_to')
    //         ->add('field_cc')
    //         ->add('field_bcc')
    //         ->add('field_subject')
    //         ->add('content')
    //         ->add('textContent')
    //         ->add('sent')
    //         ->add('createdAt')
    //         ->add('updatedAt')
    //         ->add('id')
    //         // ->add('_action', 'actions', array(
    //         //     'actions' => array(
    //         //         'show' => array(),
    //         //         'edit' => array(),
    //         //         'delete' => array(),
    //         //     )
    //         // ))
    //     ;
    // }

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
        ;
    }
}
