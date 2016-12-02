<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Librinfo\EmailBundle\Entity;

use Blast\BaseEntitiesBundle\Entity\SearchIndexEntity;

class EmailTemplateSearchIndex extends SearchIndexEntity
{

    public static $fields = ['name', 'content'];

}
