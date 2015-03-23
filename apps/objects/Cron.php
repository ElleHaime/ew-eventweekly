<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Cron extends \Core\Model
{
	const FB_USER_TASK = 'extract_facebook_events';
	const FB_CREATOR_TASK = 'extract_creators_facebook_events';
	
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
