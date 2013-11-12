<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class MemberForm extends Form
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
		$this -> addElement('text', 'email', 'Email', array('validators' => $emailValidators));
		$this -> addElement('text', 'name', 'You name');
		$this -> addElement('text', 'address', 'Address');
		$this -> addElement('text', 'phone', 'Phone');
		$this -> addElement('text', 'current_location', 'Location');
		$this -> addElement('hidden', 'prev_location');
		$this -> addElement('hidden', 'location_id');

		$this -> add(new Submit('Save'));
	}
}