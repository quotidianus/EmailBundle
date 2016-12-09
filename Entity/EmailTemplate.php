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

/**
 * EmailTemplate
 */
class EmailTemplate
{
    use BaseEntity;
    use Searchable;
    use Loggable;
    use Timestampable;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $content;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return EmailTemplate
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
     * Set content
     *
     * @param string $content
     *
     * @return EmailTemplate
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
}
