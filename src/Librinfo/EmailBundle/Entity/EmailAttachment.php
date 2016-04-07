<?php

namespace Librinfo\EmailBundle\Entity;

/**
 * EmailAttachment
 */
class EmailAttachment extends File
{
    /**
     * @var \Librinfo\EmailBundle\Entity\Email
     */
    private $email;

    // /**
    // *@var File file
    // */
    // private $file;

    /**
     * Set email
     *
     * @param \Librinfo\EmailBundle\Entity\Email $email
     *
     * @return EmailAttachment
     */
    public function setEmail(\Librinfo\EmailBundle\Entity\Email $email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return \Librinfo\EmailBundle\Entity\Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    // public function setFile(File $file)
    // {
    //     $this->file = $file;
    //
    //     return $this;
    // }
    //
    // public function getFile()
    // {
    //     return $this->file;
    // }
}
