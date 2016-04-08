<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\Traits\EmbeddedAdmin;

class EmailAttachmentAdminConcrete extends EmailAttachmentAdmin
{
    use EmbeddedAdmin;

    public function prePersist($attachment)
    {
        $this->manageFileUpload($attachment);
    }

    public function preUpdate($attachment)
    {
        $this->manageFileUpload($attachment);
    }

    private function manageFileUpload($attachment)
    {
        if ($attachment->getFile()) {
            $attachment->refreshUpdated();
        }
    }
}
