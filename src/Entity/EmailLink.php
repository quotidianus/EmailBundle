<?php

namespace Librinfo\EmailBundle\Entity;

use Blast\BaseEntitiesBundle\Entity\Traits\BaseEntity;

/**
 * EmailLink
 */
class EmailLink
{

    use BaseEntity;

    /**
     * @var string
     */
    private $destination;

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
     * Set destination
     *
     * @param string $destination
     *
     * @return EmailLink
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return EmailLink
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
     * @return EmailLink
     */
    public function setDate(\DateTime $date)
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
     * @return EmailLink
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
