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
        $this->setAttribute('class', 'form-horizontal');

        $emailValidators = array(
            'Email' => array('message' => 'Email is not valid')
        );
        $this -> addElement('text', 'extra_email', 'Additional email', array('validators' => $emailValidators));

        $nameValidators = array(
            'PresenceOf' => array('message' => 'Name is required')
        );
        $this -> addElement('text', 'name', 'Your name', array('validators' => $nameValidators));

        $this -> addElement('text', 'address', 'Address');
        $this -> addElement('text', 'phone', 'Phone');

        /*$this -> addElement('text', 'current_location', 'Location');
        $this -> addElement('hidden', 'prev_location');
        $this -> addElement('hidden', 'location_id');*/

        $this -> addElement('file', 'logo', 'Logo', array('style' => 'display:none;'));

        $this -> add(new Submit('Save', array('class' => 'btn')));
    }
}