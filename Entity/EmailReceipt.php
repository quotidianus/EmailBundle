<?php

namespace Librinfo\EmailBundle\Entity;

use Blast\BaseEntitiesBundle\Entity\Traits\BaseEntity;

/**
 * EmailReceipt
 */
class EmailReceipt
{

    use BaseEntity;

    /**
     * @var string
     */
    private $address;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \Librinfo\EmailBundle\Entity\Email
     */
    private $email;

    /**
     * Set address
     *
     * @param string $address
     *
     * @return EmailReceipt
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return EmailReceipt
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set email
     *
     * @param \Librinfo\EmailBundle\Entity\Email $email
     *
     * @return EmailReceipt
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

}
