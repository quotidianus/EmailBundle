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
     * @var String path
     */
    private $path;

    /**
     * Set path
     *
     * @param string $path
     *
     * @return EmailAttachment
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
        if ($file != null)
        {
            $this->file = base64_encode(file_get_contents($file));

            $this->name = $file->getClientOriginalName();
            $this->size = $file->getClientSize();
            $this->mimeType = $file->getClientMimeType();
        }
        return $this;
    }

    public function getFile()
    {
        return base64_decode($this->file);
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

}
