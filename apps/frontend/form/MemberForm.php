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
        $this -> addElement('text', 'extra_email', 'Additional email', array(
            'placeholder' => 'Additional email'
        ));
        $this -> addElement('text', 'name', 'Your name', array(
            'placeholder' => 'Your name'
        ));

        $this -> addElement('text', 'address', 'Address', array('placeholder' => 'Your address'));
        $this -> addElement('text', 'phone', 'Phone', array('placeholder' => 'Your phone'));

        $this -> addElement('file', 'logo', 'Logo', array('style' => 'display:none;'));

        $this -> add(new Submit('Save', array('id' => 'save-member', 'class' => 'btn')));
    }
}