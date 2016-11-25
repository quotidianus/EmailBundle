<?php

namespace Librinfo\EmailBundle\Admin;


use Blast\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailTemplateAdminConcrete extends EmailTemplateAdmin
{

    use EmbeddedAdmin;

    public $supportsPreviewMode = true;

}
