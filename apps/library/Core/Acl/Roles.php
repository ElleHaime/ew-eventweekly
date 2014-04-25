<?php

namespace Core\Acl;

class Roles extends \Phalcon\Mvc\Model
{
	public $id;
	public $type;
	public $description;
	public $is_default;
	public $extends;
	
	
	public function initialize()
	{
		$this -> setReadConnectionService('dbSlave');
		$this -> setWriteConnectionService('dbMaster');
	}
}