<?php

namespace Librinfo\EmailBundle\Entity\Traits;

trait HasEmail
{
    /**
     * @var Email
     */
    private $email;

    /**
     * Get email
     *
     * @param object $email
     *
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @return Object
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

}