<?php

namespace Librinfo\EmailBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Librinfo\DoctrineBundle\Entity\Traits\BaseEntity;

/**
 * EmailAttachment
 */
class EmailAttachment
{

    use BaseEntity;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var float
     */
    private $size;

    /**
     * @var \Librinfo\EmailBundle\Entity\Email
     */
    private $email;

    /**
     * @var UploadedFile file
     */
    private $file;


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
        if ($file != null)
        {
            $this->file = base64_encode(file_get_contents($file));
        }
        return $this;
    }

    public function getFile()
    {
        return base64_decode($this->file);
    }
    
    // used for insertion of images into content
    public function getBase64File()
    {
        return $this->file;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param float $size
     *
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @var string
     */
    private $tempId;


    /**
     * Set tempId
     *
     * @param string $tempId
     *
     * @return EmailAttachment
     */
    public function setTempId($tempId)
    {
        $this->tempId = $tempId;

        return $this;
    }

    /**
     * Get tempId
     *
     * @return string
     */
    public function getTempId()
    {
        return $this->tempId;
    }
}
