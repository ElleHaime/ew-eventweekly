<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventSite extends Model
{
	public $id;
	public $event_id;
	public $url;

	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('event_id', '\Frontend\Models\Event', 'id', array('alias' => 'site'));
	}
}