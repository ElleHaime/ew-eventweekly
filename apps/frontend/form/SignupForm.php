<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class SignupForm extends Form
{
	public function __construct($model = null)
	{
		if ($model === null){
			$model = new \Objects\Member();
		}
		parent::__construct($model);
	}
	
	public function init()
	{
		$emailValidators = array(
				'PresenceOf' => array('message' => 'Email is required'),
				'Email' => array('message' => 'Email is not valid')
		);
		$this -> addElement('text', 'email', 'Email', 
										array('validators' => $emailValidators));
		
		$passwordValidators = array(
				'PresenceOf' => array('message' => 'Password is required'),
				'StringLength' => array('min' => 2)
		);
		$this -> addElement('password', 'password', 'Password', 
										array('validators' => $passwordValidators));	


		$this -> addElement('password', 'confirm_password', 'Confirm password', 
										array('validators' => $passwordValidators));	

		$this -> add(new Submit('Sign Up'));
	}
}