<?php

namespace Frontend\Controllers;

use Core\Utils as _U;


class MemberController extends \Core\Controllers\CrudController
{
	public function listAction()
	{
		$isExternalLogged = $this -> view -> member -> network;
		if ($isExternalLogged) {
			$this -> view -> setVar('acc_external', $isExternalLogged);
			$this -> view -> setVar('acc_uid_network', 'facebook');
		}
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

	public function refreshAction()
	{
		$userData =  $this -> request -> getPost();
		
	}
}