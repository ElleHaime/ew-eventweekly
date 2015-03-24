<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Cron extends \Core\Model
{
	const FB_USER_TASK = 'extract_facebook_events';
	const FB_CREATOR_TASK = 'extract_creators_facebook_events';
	const FB_GET_ID_TASK_NAME 	= 'extract_custom_facebook_events_id';
	const FB_BY_ID_TASK_NAME	= 'extract_custom_facebook_events_data';
	
	public $id;
	public $name;
	public $description;
	public $path;
	public $member_id;
	public $parameters;
	public $state;
	public $hash;
	
	
	public function initialize()
	{
		parent::initialize();
	}
}
