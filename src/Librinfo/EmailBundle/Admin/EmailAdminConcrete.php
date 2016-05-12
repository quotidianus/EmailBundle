<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\Traits\HandlesRelationsAdmin;
use Html2Text\Html2Text;

class EmailAdminConcrete extends EmailAdmin
{
    use HandlesRelationsAdmin;

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
