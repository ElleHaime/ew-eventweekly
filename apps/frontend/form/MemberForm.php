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
            'placeholder' => 'Additional Email'
        ));
        $this -> addElement('text', 'name', 'Your name', array(
            'placeholder' => 'Your Name'
        ));

        $this -> addElement('text', 'address', 'Address', array('placeholder' => 'Your Address'));
        $this -> addElement('text', 'phone', 'Phone', array('placeholder' => 'Your Phone'));

        $this -> addElement('file', 'logo', 'Logo', array('style' => 'display:none;'));

        $this -> add(new Submit('Save', array('id' => 'save-member', 'class' => 'btn')));
    }
}