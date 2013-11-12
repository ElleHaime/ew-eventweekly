<?php

namespace Core\Acl;

class Permissions extends \Phalcon\Mvc\Model
{
	public $id;
	public $module;
	public $controller;
	public $action;
}