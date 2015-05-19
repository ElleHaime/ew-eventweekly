<?php 

namespace Frontend\Models;

use Objects\Cron as CronObject,
	Core\Utils as _U;

class Cron extends CronObject
{
	public function createUserTask($skipTimeCheck = false)
	{
		if ($session = $this -> getDI() -> getShared('session')) {
			if ($session -> has('user_token') && $session -> has('user_fb_uid') && $session -> has('memberId')) {
	            $newTask = false;
	
	            $taskSetted = self::find(array('member_id = ' . $session -> get('memberId') . ' and name =  "' . parent::FB_USER_TASK . '"'));
	            if ($taskSetted -> count() > 0) {
	                $tsk = $taskSetted -> getLast();
	                if ((time()-($tsk -> hash) > $this -> getConfig() -> application -> pingFbPeriod) || $skipTimeCheck) {
	                    $newTask = new self();
	                }
	            } else {
	                $newTask = new self();
	            }
	
	            if ($newTask) {
	                $params = ['user_token' => $session -> get('user_token'),
	                           'user_fb_uid' => $session -> get('user_fb_uid'),
	                           'member_id' => $session -> get('memberId')];
	                $task = ['name' => parent::FB_USER_TASK,
	                         'parameters' => serialize($params),
	                         'state' => 0,
	                         'member_id' => $session -> get('memberId'),
	                         'hash' => time()];
	                
	                $newTask -> assign($task);
	                $newTask -> save();
	            }
	        }
		}
		
		$this -> createCreatorTask();
		$this -> createCustomTask();
		
		return true;
	}
	
	public function createCreatorTask()
	{
		if ($session = $this -> getDI() -> getShared('session')) {
			if ($session -> has('user_token') && $session -> has('user_fb_uid') && $session -> has('memberId')) {
	            $newTask = false;
			
				$taskSetted = self::find(array('name = "' . parent::FB_CREATOR_TASK . '"'));
				if ($taskSetted -> count() > 0) {
					$tsk = $taskSetted -> getLast();
					if (time()-($tsk -> hash) > 43200) {
						$newTask = new self();
					}
				} else {
					$newTask = new self();
				}
	
				if ($newTask) {
					$params = ['user_token' => $session -> get('user_token'),
								'user_fb_uid' => $session -> get('user_fb_uid'),
								'member_id' => $session -> get('memberId')];
					$task = ['name' => parent::FB_CREATOR_TASK,
								'parameters' => serialize($params),
								'state' => 0,
								'member_id' => $session -> get('memberId'),
								'hash' => time()];

					$newTask -> assign($task);
					$newTask -> save();
				}
			}
		}
	
		return true;
	}
	
	
	public function createCustomTask()
	{
		if ($session = $this -> getDI() -> getShared('session')) {
			if ($session -> has('user_token') && $session -> has('user_fb_uid') && $session -> has('memberId')) {
				$newTask = false;
					
				$taskSetted = self::find(array('name = "' . parent::FB_GET_ID_TASK_NAME . '"'));
				if ($taskSetted -> count() > 0) {
					$tsk = $taskSetted -> getLast();
					if (time()-($tsk -> hash) > 80200) {
						$newTask = new self();
					}
				} else {
					$newTask = new self();
				}
	
				if ($newTask) {
					$params = ['user_token' => $session -> get('user_token'),
						'user_fb_uid' => $session -> get('user_fb_uid'),
						'member_id' => $session -> get('memberId')];
					$task = ['name' => parent::FB_GET_ID_TASK_NAME,
						'parameters' => serialize($params),
						'state' => 0,
						'member_id' => $session -> get('memberId'),
						'hash' => time()];
	
					$newTask -> assign($task);
					$newTask -> save();
				}
			}
		}
	
		return true;
	}
	
}
