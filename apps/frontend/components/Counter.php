<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component;

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

		foreach($this -> userCounters as $counterName => $options) {
			$counterCache = $this -> composeCounterName($counterName);
			$counterView = $counterName;

			$this -> cacheData -> exists($counterCache) ?
                $result[$counterView] = $this -> cacheData -> get($counterCache) :
                $result[$counterView] = 0;

            if ($setView) {
            	$this -> view -> setVar($counterView, $result[$counterView]);
            } 
		}

		return $result;
	}

	public function increaseUserCounter($counter, $val = 1)
	{
		$cacheCounter = $this -> composeCounterName($counter);

 		$this -> cacheData -> exists($cacheCounter) ?
            $this -> cacheData -> save($cacheCounter, $this -> cacheData -> get($cacheCounter)+(int)$val) :
            $this -> cacheData -> save($cacheCounter, (int)$val);
	}

	public function decreaseUserCounter($counter, $val = 1)
	{
		$cacheCounter = $this -> composeCounterName($counter);

        if ($this -> cacheData -> exists($cacheCounter)) {
            if ($this -> cacheData -> get($cacheCounter) > 0) {
                $this -> cacheData -> save($cacheCounter, $this -> cacheData -> get($cacheCounter)-(int)$val);
            }
        }
	}

	public function get($counter) 
	{
		$cacheCounter = $this -> composeCounterName($counter);

		return $this -> cacheData -> get($cacheCounter);
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