<?php

namespace Librinfo\EmailBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Librinfo\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailTemplateAdminConcrete extends EmailTemplateAdmin
{
  use EmbeddedAdmin;
  
  public $supportsPreviewMode = true;
}
