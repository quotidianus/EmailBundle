<?php

namespace Librinfo\EmailBundle\Admin;


use Librinfo\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailTemplateAdminConcrete extends EmailTemplateAdmin
{

    use EmbeddedAdmin;

    public $supportsPreviewMode = true;

}
