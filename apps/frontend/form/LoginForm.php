<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class LoginForm extends Form
{
	public function init()
	{
		$this -> addElement('text', 'email', 'Email', array('placeholder' => 'Email'));
		$this -> addElement('password', 'password', 'Password', array('placeholder' => 'Password'));
		
		$this -> add(new Submit('Login'));
	}
}