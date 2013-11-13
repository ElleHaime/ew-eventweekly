<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit;

class CampaignForm extends Form
{
	public function __construct($model = null)
	{
		if ($model === null){
			$model = new \Objects\Campaign();
		}
		parent::__construct($model);
	}
	
	public function init()
	{
		$nameValidators = array(
				'PresenceOf' => array('message' => 'Name is required')
		);
		$this -> addElement('text', 'name', 'Name', array('validators' => $nameValidators));
		$this -> addElement('textarea', 'description', 'Description');		
		$this -> addElement('text', 'address', 'Address');
		$this -> addElement('text', 'current_location', 'Location');
		$this -> addElement('hidden', 'prev_location');
		$this -> addElement('hidden', 'member_id');
		$this -> addElement('hidden', 'location_id');
		
		$this -> add(new Submit('Save'));
	}
}