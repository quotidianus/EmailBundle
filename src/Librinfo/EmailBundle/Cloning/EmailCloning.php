<?php

namespace Librinfo\EmailBundle\Cloning;

use Librinfo\EmailBundle\Entity\Email;

class EmailCloning
{
    /**
     * Clone Email excluding id for duplicate action
     * 
     * @param Email $email
     * @return Email
     */
    public function cloneEmail(Email $email)
    {
        $newEmail = new Email();

        $newEmail->setFieldFrom($email->getFieldFrom());
        $newEmail->setFieldTo($email->getFieldTo());
        $newEmail->setFieldCc($email->getFieldCc());
        $newEmail->setfieldBcc($email->getFieldBcc());
        $newEmail->setFieldSubject($email->getFieldSubject());
        $newEmail->setContent($email->getContent());
        $newEmail->setAttachments($email->getAttachments());

        return $newEmail;
    }

}
