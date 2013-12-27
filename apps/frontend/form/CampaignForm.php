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
		$this -> addElement('hidden', 'id');		

		$this -> addElement('hidden', 'logo', 'add image');	
		$this -> addElement('file', 'add-img-upload', 'upload',
								array('style' => 'display:none;'));	

		$nameValidators = array(
				'PresenceOf' => array('message' => 'Name is required')
		);
		$this -> addElement('text', 'name', 'Name',
                                array('placeholder' => 'Campaign title',
                                      'validators' => $nameValidators));
		$this -> addElement('textarea', 'description', 'Description',
								array('placeholder' => 'add description',
									  'class' => 'resizable field-big'));
		$this -> addElement('text', 'address', 'Address',
								array('placeholder' => 'Choose Address'));
		$this -> addElement('hidden', 'address-coords');	
		$this -> addElement('text', 'location', 'Location',
								array('placeholder' => 'Choose location'));
		$this -> addElement('hidden', 'location_latitude');
		$this -> addElement('hidden', 'location_longitude');

		$this -> addElement('text', 'campaign_contacts', 'Contacts',
								array('style' => 'display:none;'));

		$this -> addElement('text', 'campaign_events', 'Events',
								array('style' => 'display:none;'));		
		
		$this -> add(new Submit('Save'));
	}
}