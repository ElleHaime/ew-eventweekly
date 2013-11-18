<?php 

namespace Frontend\Models;

use Objects\Event as EventObject;

class Event extends EventObject
{

	public static $eventStatus = array(0 => 'inactive',
							  		   1 => 'active');

	public static $eventRecurring = array('0' => 'one time',
										  '1' => 'every day',
										  '7' => 'every week');
} 