<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventImage extends Model
{
	public $id;
	public $event_id;
	public $image;
	public $type;

	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('event_id', '\Frontend\Models\Event', 'id', array('alias' => 'event'));
	}
}