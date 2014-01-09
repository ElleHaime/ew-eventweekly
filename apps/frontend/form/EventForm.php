<?php

namespace Frontend\Form;

use Core\Form,
	Phalcon\Forms\Element\Submit,
	Core\Utils as _U;

class EventForm extends Form
{
	public function __construct($model = null)
	{
		if ($model === null){
			$model = new \Objects\Event();
		}
		parent::__construct($model);
	}
	
	public function init()
	{
		$this -> addElement('hidden', 'id');		

		$this -> addElement('hidden', 'logo', 'Add image');
		$this -> addElement('file', 'add-img-upload', 'upload',
								array('style' => 'display:none;'));	

		$nameValidators = array(
				'PresenceOf' => array('message' => 'Name is required')
		);
		$this -> addElement('text', 'name', 'Name', 
								array('validators' => $nameValidators,
									   'placeholder' => 'Main title'));

		$this -> addElement('radio', 'recurring', 'Recurring', 
								array('options' => \Frontend\Models\Event::$eventRecurring)); 
		
		$this -> addElement('text', 'location', 'Location',
								array('placeholder' => 'Choose location'));
		$this -> addElement('hidden', 'location_latitude');
		$this -> addElement('hidden', 'location_longitude');
		
		$this -> addElement('text', 'address', 'Address', 
								array('placeholder' => 'Choose address'));
		$this -> addElement('hidden', 'address-coords');		

		$this -> addElement('text', 'venue', 'Venue',
								array('placeholder' => 'Choose venue'));
		$this -> addElement('hidden', 'venue_latitude');
		$this -> addElement('hidden', 'venue_longitude');

		$this -> addElement('check', 'event_status', 'Publish event immediately');

		$this -> addElement('textarea', 'description', 'Description', 
								array('placeholder' => 'Add description',
									  'class' => 'resizable field-big'));

		$this -> addElement('text', 'event_site', 'Event web site',
								array('style' => 'display:none;'));

		$this -> addElement('date', 'start_date', 'Start date',
								array('data-format' => 'dd/MM/yyyy',
									  'data-type' => 'event_date',
									  'placeholder' => 'Start date'));
		
		$this -> addElement('date', 'start_time', 'Start time',
								array('data-format' => 'hh:mm:ss',
									  'data-type' => 'event_time',
									  'placeholder' => 'Start time'));
		
		$this -> addElement('date', 'end_date', 'End date',
								array('data-format' => 'dd/MM/yyyy',
									  'data-type' => 'event_date',
									  'placeholder' => 'End date'));

		$this -> addElement('date', 'end_time', 'End time',
								array('data-format' => 'hh:mm:ss',
									  'data-type' => 'event_time',
									  'placeholder' => 'End time'));

		$this -> addElement('select', 'event_category', 'Suggest category', 
								array('options' => \Frontend\Models\Category::find(),
									  'using' => array('id', 'name')));
		
		$this -> addElement('hidden', 'category');	

		$this -> addElement('select', 'campaign_id', 'Campaign', 
								array('options' => $this -> session -> get('member') -> campaign,
									  'using' => array('id', 'name')));
		
		$this -> add(new Submit('Save'));
	}
}