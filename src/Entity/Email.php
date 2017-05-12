<?php

/*
 * Copyright (C) 2015-2016 Libre Informatique
 *
 * This file is licenced under the GNU GPL v3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Librinfo\EmailBundle\Entity;

use Blast\BaseEntitiesBundle\Entity\Traits\BaseEntity;
use Blast\BaseEntitiesBundle\Entity\Traits\Loggable;
use Blast\BaseEntitiesBundle\Entity\Traits\Searchable;
use Blast\BaseEntitiesBundle\Entity\Traits\Timestampable;
use Blast\OuterExtensionBundle\Entity\Traits\OuterExtensible;
use Doctrine\Common\Collections\ArrayCollection;
use Librinfo\MediaBundle\Entity\File;

/**
 * Email
 */
class Email extends Spoolable
{
    use BaseEntity;
    use OuterExtensible;
    use EmailExtension;
    use Searchable;
    use Loggable;
    use Timestampable;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attachments;

    /**
     * @var bool
     */
    private $isTest;

    /**
     * @var string
     */
    private $testAddress;

    /**
     * @var bool
     */
    private $isTemplate;

    /**
     * @var string
     */
    private $newTemplateName;

    /**
     * @var EmailTemplate
     */
    private $template;

    /**
     * @var bool
     */
    private $tracking;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $receipts;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $links;

    public function initCollections()
    {
        $this->receipts = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->sent = false;
        $this->isTemplate = false;
        $this->initCollections();
        $this->initOuterExtendedClasses();
    }

    // implementation of __clone for duplication
    public function __clone()
    {
        $this->id = null;
        $this->initCollections();
    }
    
    public function __toString()
    {
        return $this->getFieldSubject();
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
        return $this->field_subject ? $this->field_subject : '<no subject>';
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
     * @param \Librinfo\MediaBundle\Entity\File $attachment
     *
     * @return Email
     */
    public function addAttachment(File $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }
    
    public function addLibrinfoFile(File $file)
    {
        return $this->addAttachment($file);
    }

    /**
     * Remove attachment
     *
     * @param \Librinfo\MediaBundle\Entity\File $attachment
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAttachment(File $attachment)
    {

        return $this->attachments->removeElement($attachment);
    }
    
    public function removeLibrinfoFile(File $file)
    {
        return $this->removeAttachment($file);
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
    
    public function getLibrinfoFiles()
    {
        return $this->getAttachments();
    }

    /**
     * Set attachments
     * Used for duplicating purposes
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    public function getIsTest()
    {
        return $this->isTest;
    }

    public function setIsTest($isTest)
    {
        $this->isTest = $isTest;
    }

    public function getTestAddress()
    {
        return $this->testAddress;
    }

    public function setTestAddress($testAddress = NULL)
    {
        $this->testAddress = $testAddress;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template = NULL)
    {
        $this->template = $template;
    }

     public function getIsTemplate()
    {
        return $this->isTemplate;
    }

    public function setIsTemplate($isTemplate)
    {
        $this->isTemplate = $isTemplate;
    }

    public function getNewTemplateName()
    {
        return $this->newTemplateName;
    }

    public function setNewTemplateName($newTemplateName)
    {
        $this->newTemplateName = $newTemplateName;
    }

    /**
     * Set tracking
     *
     * @param bool $tracking
     *
     * @return Email
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * Get tracking
     *
     * @return bool
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * Add receipt
     *
     * @param \Librinfo\EmailBundle\Entity\EmailReceipt $receipt
     *
     * @return Email
     */
    public function addReceipt(\Librinfo\EmailBundle\Entity\EmailReceipt $receipt)
    {
        $this->receipts[] = $receipt;

        return $this;
    }

    /**
     * Remove receipt
     *
     * @param \Librinfo\EmailBundle\Entity\EmailReceipt $receipt
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeReceipt(\Librinfo\EmailBundle\Entity\EmailReceipt $receipt)
    {
        return $this->receipts->removeElement($receipt);
    }

    /**
     * Get receipts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceipts()
    {
        return $this->receipts;
    }

    /**
     * Add link
     *
     * @param \Librinfo\EmailBundle\Entity\EmailLink $link
     *
     * @return Email
     */
    public function addLink(\Librinfo\EmailBundle\Entity\EmailLink $link)
    {
        $this->links[] = $link;

        return $this;
    }

    /**
     * Remove link
     *
     * @param \Librinfo\EmailBundle\Entity\EmailLink $link
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeLink(\Librinfo\EmailBundle\Entity\EmailLink $link)
    {
        return $this->links->removeElement($link);
    }

    /**
     * Get links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLinks()
    {
        return $this->links;
    }
}
