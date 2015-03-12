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
        
        $this -> addElement('text', 'extra_email', 'Additional email', ['placeholder' => 'Additional email',
        												  'class' => 'input-registration-control']);
        
        $this -> addElement('text', 'name', 'Your name', ['placeholder' => 'Your name',
        												  'class' => 'input-registration-control']);

        $this -> addElement('text', 'address', 'Address', ['placeholder' => 'Your address',
        													'class' => 'input-registration-control']);
        $this -> addElement('text', 'phone', 'Phone', ['placeholder' => 'Your phone',
        													'class' => 'input-registration-control']);

        $this -> addElement('file', 'logo', 'Logo', array('style' => 'display:none;'));

        $this -> add(new Submit('Save', array('id' => 'save-member', 'class' => 'btn')));
    }
}