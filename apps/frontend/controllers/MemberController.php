<?php

namespace Frontend\Controllers;

use Core\Utils as _U;


class MemberController extends \Core\Controllers\CrudController
{
	public function listAction()
	{
	}
	
	public function loadObject()
	{
		$this -> obj = $this -> session -> get('member');
		$this -> setDependencyProperty($this -> obj -> getDependency());
		
		return $this;
	}
	
	public function loadRedirect()
	{
		$this -> response -> redirect('profile');
	}
}