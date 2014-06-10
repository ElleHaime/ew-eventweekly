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
	
	public function saveNewIp($locationId, $ip)
	{
		if ($ip != '') {
			$ipExists = self::findFirst('ip = "' . $ip . '"');
			
			if (!$ipExists) {
				$newIp = new self;			
				$newIp -> assign([
						'location_id' => $locationId,
						'ip' => $ip
						]);
				$newIp -> save();
			}
		}
	}
}