<?php

namespace Librinfo\EmailBundle\Entity\OuterExtension;

trait HasEmail
{
    /**
     * @var Email
     */
    private $email;

    /**
     * Get email
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
     * @param Email $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

}