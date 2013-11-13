<?php

namespace Core\Acl;

class RolesPermissions extends \Phalcon\Mvc\Model
{
	public $id;
	public $roles_id;
	public $permissions_id;
}