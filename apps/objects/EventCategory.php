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
		$this -> belongsTo('category_id', '\Object\Category', 'id', array('alias' => 'category'));
		$this -> belongsTo('event_id', '\Object\Event', 'id', array('alias' => 'event'));
	}
}