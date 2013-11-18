<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventMember extends Model
{
	const
	JOIN    = 1,
	MAYBE   = 2,
	DECLINE = 3;

	public $id;
	public $event_id;
	public $member_id;
	public $member_status;

	public function initialize()
	{
		$this -> hasMany('event_id', '\Object\Event', 'id', array('alias' => 'eventpart'));
		$this -> hasMany('member_id', '\Object\Member', 'id', array('alias' => 'memberpart'));
	}

	public function createOnChange($argument)
	{
		/*
		$isEventMemberExist = self::findFirst(array('member_id = "'. $argument . '"'));
		if (!$isEventMemberExist) {
			$this -> assign(array('member_id' => $argument,
				'member_status' => $memberStatus));
			$this -> save();

			return $this -> id;
		} else {
			return $isEventMemberExist -> id;
		}
		*/
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