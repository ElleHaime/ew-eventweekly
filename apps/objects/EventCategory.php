<?php 

namespace Objects;

use Core\Model;

class EventCategory extends Model
{
	public $id;

	public $event_id;

	public $category_id;
	
	public function initialize()
	{
        $this->belongsTo('event_id', '\Objects\Event', 'id', array('alias' => 'event_category'));
        $this->belongsTo('category_id', '\Objects\Category', 'id');
	}
}