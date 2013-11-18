<?php

namespace Core;

use Frontend\Models\Members,
	Core\Acl,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher;

class Auth extends Plugin
{
	public function checkAccessByEmail($credentials)
	{
		$member = Members::findFirst(array(
				'email = ?0',
				'bind' => (array)$credentials['email']));

		if ($member == false) {
			throw new \Exception('Wrong email/password combination');
			return false;
		}

		if (!$this -> security -> checkHash($credentials['pass'], $member -> pass)) {
			throw new \Exception('Wrong email/password combination');
			return false;
		}

		return $member;
	}
	

	public function checkAccessByHash()
	{

	}

	public function checkAccessByCookies()
	{

	}
}
