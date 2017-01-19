<?php

namespace Librinfo\EmailBundle\Entity\OuterExtension;

use Doctrine\Common\Collections\Collection;

trait HasEmailMessages
{
    /**
     * @var Collection
     */
    private $emailMessages;

    /**
     * Add email message
     *
     * @param object $emailMessage
     *
     * @return self
     */
    public function addEmailMessage($emailMessage)
    {
        //$emailMessage->addOrganism($this); // TODO: synchronously updating the inverse side
        $this->emailMessages[] = $emailMessage;
        return $this;
    }

    /**
     * Remove email message
     *
     * @param object $emailMessage
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEmailMessage($emailMessage)
    {
        //$emailMessage->removeOrganism($this); // TODO: synchronously updating the inverse side
        return $this->emailMessages->removeElement($emailMessage);
    }

    /**
     * Get email messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmailMessages()
    {
        return $this->emailMessages;
    }

}