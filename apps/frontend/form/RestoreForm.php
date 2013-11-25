<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class RestoreForm extends Form
{
	public function init()
	{
		$emailValidators = array(
				'PresenceOf' => array('message' => 'Email is required'),
				'Email' => array('message' => 'Email is not valid')
		);
		$this -> addElement('text', 'email', 'Email', array('validators' => $emailValidators));
		
		$this -> add(new Submit('Restore'));
	}
}