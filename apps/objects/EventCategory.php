<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventCategory extends Model
{
	public $id;
	public $event_id;
	public $category_id = 1; 
	
	public function initialize()
	{
		parent::initialize();
				
        $this -> belongsTo('event_id', '\Frontend\Models\Event', 'id', array('alias' => 'event_category'));
        $this -> belongsTo('category_id', '\Frontend\Models\Category', 'id', array('alias' => 'eventpart2'));
	}
}