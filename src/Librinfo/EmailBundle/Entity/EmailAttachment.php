<?php

namespace Librinfo\EmailBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * EmailAttachment
 */
class EmailAttachment extends File
{
    const ATTACHMENTS_DIR = __DIR__;

    /**
     * @var \Librinfo\EmailBundle\Entity\Email
     */
    private $email;

    /**
    *@var UploadedFile file
    */
    private $file;

    /**
    *@var DateTime updated
    */
    private $updated;

    /**
     * Set email
     *
     * @param DateTime updated
     *
     * @return updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return DateTime updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

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

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
 * Manages the copying of the file to the relevant place on the server
 */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and target filename as params
        $this->getFile()->move(
            self::ATTACHMENTS_DIR,
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->filename = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setUpdated(new \DateTime());
    }
}
