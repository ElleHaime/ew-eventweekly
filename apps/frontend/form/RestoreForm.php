<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Submit,
	Phalcon\Forms\Element\Hidden,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical;

class RestoreForm extends Form
{
	public function init()
	{
		$email = new Text('email');
		$email -> setLabel('Email');
		$email -> addValidators(
			array(
				new PresenceOf(array(
					'message' => 'Email is required'
				)),
				new Email(array(
					'message' => 'Email is not valid'
				))
			)
		);
		$this -> add($email);

		$this -> add(new Submit('Restore'));
	}
}