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
        $this->hasMany('id', '\Objects\EventTag', 'event_id', array('alias' => 'event_tag'));
    }
}