<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component;

class Counter extends Component
{
	public $userCounters = [
		'eventsGTotal' => ['member' => false]
	];


	public function __construct()
	{
		$this -> cacheData = $this -> getDI() -> get('cacheData');
	}
	
	public function setUserCounters($setView = true)
	{
		$result = [];
		
		$ev = new \Frontend\Models\Event();
		$ev -> setCacheTotal();
		$result['eventsGTotal'] = $this -> cacheData -> get($counterName);

		return $result;
	}

	public function get($counter) 
	{
		if ($counter == 'eventsGTotal') {
			$ev = new \Frontend\Models\Event();
			$ev -> setCacheTotal();
			return $this -> cacheData -> get($counter);
		} else {
			return;
		}
	}
}