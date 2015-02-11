<?php 

namespace Frontend\Models;

use Objects\Cron as CronObject;

class Cron extends CronObject
{
	public function createUserTask()
	{
		if ($session = $this -> getDI() -> getShared('session')) {
			if ($session -> has('user_token') && $session -> has('user_fb_uid') && $session -> has('memberId')) {
	            $newTask = false;
	
	            $taskSetted = self::find(array('member_id = ' . $session -> get('memberId') . ' and name =  "' . parent::FB_USER_TASK . '"'));
	            if ($taskSetted -> count() > 0) {
	                $tsk = $taskSetted -> getLast();
	                if (time()-($tsk -> hash) > $this -> getConfig() -> application -> pingFbPeriod) {
	                    $newTask = new self();
	                }
	            } else {
	                $newTask = new self();
	            }
	
	            if ($newTask) {
	                $params = ['user_token' => $session -> get('user_token'),
	                           'user_fb_uid' => $session -> get('user_fb_uid'),
	                           'member_id' => $session -> get('memberId')];
	                $task = ['name' => 'extract_facebook_events',
	                         'parameters' => serialize($params),
	                         'state' => 0,
	                         'member_id' => $session -> get('memberId'),
	                         'hash' => time()];
	                
	                $newTask -> assign($task);
	                $newTask -> save();
	            }
	        }
		}
	}
}