<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventRating extends Model
{
	public $id;
	public $event_id;
	public $location_id;
	public $rank = 0; 
	
	public function initialize()
	{
		parent::initialize();
				
        $this -> belongsTo('event_id', '\Objects\Event', 'id', array('alias' => 'event_rating'));
	}
}