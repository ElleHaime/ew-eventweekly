<?php

namespace Objects;

use Core\Model;

class Tag extends Model
{
    public $id;

    public $key;

    public $name;

    public $category_id;

    public function initialize()
    {
		parent::initialize();
		    	
        $this->hasMany('id', '\Objects\EventTag', 'event_id', array('alias' => 'event_tag'));
        $this->hasMany('id', '\Objects\Keyword', 'tag_id', array('alias' => 'tag_keyword'));
    }
}