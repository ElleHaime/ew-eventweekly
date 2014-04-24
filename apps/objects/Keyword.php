<?php

namespace Objects;

use Core\Model;

class Keyword extends Model
{
    public $id;

    public $tag_id;

    public $key;
    
    
    public function initialize()
    {
    	parent::initialize();
    }
}