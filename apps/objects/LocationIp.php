<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class LocationIp extends Model
{
	public $id;
	public $ip;	
	public $location_id;


	public function initialize()
	{
		parent::initialize();
		$this -> hasMany('location_id', '\Object\Location', 'id', array('alias' => 'location'));
	}
}