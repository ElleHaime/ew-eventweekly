<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\EventMemberCounter;

class Counter extends Component
{
	public $userCounters = [
		'eventsGTotal' => ['member' => false], 
		'userEventsCreated' => ['member' => true],
		'userEventsLiked' => ['member' => true],
		'userEventsGoing' => ['member' => true],
		'userFriendsGoing' => ['member' => true]
	];


	public function __construct()
	{
		$this -> cacheData = $this -> getDI() -> get('cacheData');
	}
	
	
	public function setUserCounters($setView = true)
	{
		$result = [];
		
		if ($this -> getDI() -> get('session') -> has('memberId')) {
			$ec = new EventMemberCounter();
			$model = $ec -> getMemberCounter();
			
			if ($model) {
				foreach($this -> userCounters as $counterName => $options) {
					if ($counterName == 'eventsGTotal') {
						$ev = new \Frontend\Models\Event();
						$ev -> setCacheTotal();
						$result[$counterName] = $this -> cacheData -> get($counterName);
					} else {
						$result[$counterName] = $model -> $counterName;
					}
					if ($setView) {
						$this -> view -> setVar($counterName, $result[$counterName]);
					}
				}
			}
		} else {
			foreach($this -> userCounters as $counterName => $options) {
				if ($counterName == 'eventsGTotal') {
					$ev = new \Frontend\Models\Event();
					$ev -> setCacheTotal();
					$result[$counterName] = $this -> cacheData -> get($counterName);
				} else {
					$result[$counterName] = 0;
				}
				
				if ($setView) {
					$this -> view -> setVar($counterName, $result[$counterName]);
				}
			}
		}

		return $result;
	}

	public function increaseUserCounter($counter, $val = 1)
	{
		if ($counter != 'eventsGTotal') {
			$ec = new EventMemberCounter();
			$eventCounter = $ec -> getMemberCounter();
			if ($eventCounter) {
				$eventCounter -> $counter = $eventCounter -> $counter + (int)$val;
				$eventCounter -> save();
			}
		}
	}

	public function decreaseUserCounter($counter, $val = 1)
	{
       if ($counter != 'eventsGTotal') {
	 		$ec = new EventMemberCounter();
			$eventCounter = $ec -> getMemberCounter();
	        if ($eventCounter) {
	        	if ((int)$eventCounter -> $counter != 0) {
		        	$eventCounter -> $counter = $eventCounter -> $counter - (int)$val;
		        	$eventCounter -> save();
	        	}
	        }
       }
	}

	public function get($counter) 
	{
		if ($counter != 'eventsGTotal') {
			$ec = new EventMemberCounter();
			$eventCounter = $ec -> getMemberCounter();
			if ($eventCounter) {
				return $eventCounter -> counter;				
			}
		} else {
			$ev = new \Frontend\Models\Event();
			$ev -> setCacheTotal();
			return $this -> cacheData -> get($counter);
		}
	}


	private function composeCounterName($counter)
	{
		if ($this -> getDI() -> get('session') -> has('memberId') && $this -> userCounters[$counter]['member']) {
			$cacheCounter = $counter . '.' . $this -> getDI() -> get('session') -> get('memberId');
		} else {
			$cacheCounter = $counter;
		}

		return $cacheCounter;
	}
}