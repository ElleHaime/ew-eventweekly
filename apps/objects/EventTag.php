<?php

namespace Objects;

use Core\Model;

class EventTag extends Model
{
    public $id;

    public $event_id;

    public $tag_id = 1;

    public function initialize()
    {
        $this->belongsTo('event_id', '\Objects\Event', 'id', array('alias' => 'event_tag'));
        $this->belongsTo('tag_id', '\Objects\Tag', 'id', array('alias' => 'event_tag'));
    }
}