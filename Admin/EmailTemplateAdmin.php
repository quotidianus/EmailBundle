<?php

namespace Librinfo\EmailBundle\Admin;

use Blast\CoreBundle\Admin\CoreAdmin;
use Blast\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailTemplateAdmin extends CoreAdmin
{
   use EmbeddedAdmin;

   public $supportsPreviewMode = true;

}
