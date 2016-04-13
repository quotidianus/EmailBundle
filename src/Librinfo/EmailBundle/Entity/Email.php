<?php

namespace Librinfo\EmailBundle\Entity;

use Librinfo\DoctrineBundle\Entity\Traits\BaseEntity;
use Librinfo\DoctrineBundle\Entity\Traits\Searchable;
use Librinfo\DoctrineBundle\Entity\Traits\Loggable;
use Librinfo\UserBundle\Entity\Traits\Traceable;
use Librinfo\EmailBundle\Entity\Spoolable;

/**
 * Email
 */
class Email extends Spoolable
{
    use BaseEntity;
    use Searchable;
    use Loggable;
    use Traceable;

    /**
     * @var string
     */
    private $field_from;

    /**
     * @var string
     */
    private $field_to;

    /**
     * @var string
     */
    private $field_cc;

    /**
     * @var string
     */
    private $field_bcc;

    /**
     * @var string
     */
    private $field_subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $textContent;

    /**
     * @var bool
     */
    private $sent;

    /**
     * @var Collection
     */
    private $attachments;

    /**
     * constructor
     */
    public function __construct() {

        $this->sent = false;
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set fieldFrom
     *
     * @param string $fieldFrom
     *
     * @return Email
     */
    public function setFieldFrom($fieldFrom)
    {
        $this->field_from = $fieldFrom;

        return $this;
    }

    /**
     * Get fieldFrom
     *
     * @return string
     */
    public function getFieldFrom()
    {
        return $this->field_from;
    }

    /**
     * Set fieldTo
     *
     * @param string $fieldTo
     *
     * @return Email
     */
    public function setFieldTo($fieldTo)
    {
        $this->field_to = $fieldTo;

        return $this;
    }

    /**
     * Get fieldTo
     *
     * @return string
     */
    public function getFieldTo()
    {
        return $this->field_to;
    }

    /**
     * Set fieldCc
     *
     * @param string $fieldCc
     *
     * @return Email
     */
    public function setFieldCc($fieldCc)
    {
        $this->field_cc = $fieldCc;

        return $this;
    }

    /**
     * Get fieldCc
     *
     * @return string
     */
    public function getFieldCc()
    {
        return $this->field_cc;
    }

    /**
     * Set fieldBcc
     *
     * @param string $fieldBcc
     *
     * @return Email
     */
    public function setFieldBcc($fieldBcc)
    {
        $this->field_bcc = $fieldBcc;

        return $this;
    }

    /**
     * Get fieldBcc
     *
     * @return string
     */
    public function getFieldBcc()
    {
        return $this->field_bcc;
    }

    /**
     * Set fieldSubject
     *
     * @param string $fieldSubject
     *
     * @return Email
     */
    public function setFieldSubject($fieldSubject)
    {
        $this->field_subject = $fieldSubject;

        return $this;
    }

    /**
     * Get fieldSubject
     *
     * @return string
     */
    public function getFieldSubject()
    {
        return $this->field_subject;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Email
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set textContent
     *
     * @param string $textContent
     *
     * @return Email
     */
    public function setTextContent($textContent)
    {
        $this->textContent = $textContent;

        return $this;
    }

    /**
     * Get textContent
     *
     * @return string
     */
    public function getTextContent()
    {
        return $this->textContent;
    }

    /**
     * Set sent
     *
     * @param bool $sent
     *
     * @return Email
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return bool
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Add attachment
     *
     * @param \Librinfo\EmailBundle\Entity\EmailAttachment $attachment
     *
     * @return Email
     */
    public function addAttachment(\Librinfo\EmailBundle\Entity\EmailAttachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \Librinfo\EmailBundle\Entity\EmailAttachment $attachment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAttachment(\Librinfo\EmailBundle\Entity\EmailAttachment $attachment)
    {

        return $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
