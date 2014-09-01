<?php

namespace Core\Traits;

use Core\Utils as _U;

trait ShardingRestorer {
	
	public function restoreStructure()
	{
		$events = parent::find('start_date > now() or end_date > now()');
_U::dump($events -> count());
	}
}
