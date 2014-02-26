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
		$this -> belongsTo('event_id', '\Object\Event', 'id', array('alias' => 'event'));
	}
	
	public function beforeValidationOnCreate()
	{
	}
	
	public function afterSave()
	{
	}
	
	public function validation()
	{
	}
}