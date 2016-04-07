<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\Traits\HandlesRelationsAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

class EmailAdminConcrete extends EmailAdmin
{
    use HandlesRelationsAdmin;
    
    protected function configureRoutes(RouteCollection $collection)
    {
    $collection->add('send', $this->getRouterIdParameter().'/send');
    }
}
