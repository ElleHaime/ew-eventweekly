<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class ResetForm extends Form
{
	public function init()
	{
		$passwordValidators = array(
				'PresenceOf' => array('message' => 'Password is required'),
				'StringLength' => array('min' => 2)
		);
		$this -> addElement('password', 'password', 'Password', array('validators' => $passwordValidators));		

		$this -> add(new Submit('Reset password'));
	}
}