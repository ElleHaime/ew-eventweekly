<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Location extends Model
{
	public $id;
	public $name;
	public $type;
	public $coordinates;
	public $lat;
	public $lon;
	public $parent_id = 0;


	public function initialize()
	{
		$this -> belongsTo('id', '\Objects\Member', 'location_id');
		$this -> belongsTo('id', '\Objects\Event', 'location_id');
		$this -> hasMany('id', '\Objects\Campaign', 'location_id', array('alias' => 'campaignDependency'));
	}
	
	public function createOnChange($argument)
	{
		$isLocationExists = self::findFirst(array('name = "'. $argument . '"'));
		if (!$isLocationExists) {
			$this -> assign(array('name' => $argument,
								  'type' => 'city'));
			$this -> save();
			
			return $this -> id;
		} else {
			return $isLocationExists -> id;
		}
	}
}
