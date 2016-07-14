<?php

namespace Objects;

use Core\Model;

class VenueTag extends Model
{
    public $id;

    public $venue_id;

    public $tag_id = 1;

    public function initialize()
    {
		parent::initialize();
		    	
        $this->belongsTo('venue_id', '\Frontend\Models\Venue', 'id', array('alias' => 'venue_tag'));
        $this->belongsTo('tag_id', '\Objects\Tag', 'id', array('alias' => 'venue_tag'));
    }
}