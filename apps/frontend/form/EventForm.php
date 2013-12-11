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
		$this -> addElement('file', 'logo', 'add image',
								array('style' => 'display:none;',
									  'value' => 'upload'));		

		$nameValidators = array(
				'PresenceOf' => array('message' => 'Name is required')
		);
		$this -> addElement('text', 'name', 'Name', 
								array('validators' => $nameValidators,
									   'placeholder' => 'main title'));

		$this -> addElement('radio', 'recurring', 'Recurring', 
								array('options' => \Frontend\Models\Event::$eventRecurring)); 
		
		$this -> addElement('text', 'location', 'Location',
								array('placeholder' => 'Choose location'));
		$this -> addElement('hidden', 'location-coords');
		
		$this -> addElement('text', 'address', 'Address', 
								array('placeholder' => 'Choose Address'));
		$this -> addElement('hidden', 'address-coords');		

		$this -> addElement('text', 'venue', 'Venue',
								array('placeholder' => 'Choose Venue'));
		$this -> addElement('hidden', 'venue-coords');

		$this -> addElement('text', 'event_site', 'Event sites', 
								array('style' => 'display:none;'));	

		$this -> addElement('check', 'event_status', 'Publish event immediately');

		$this -> addElement('textarea', 'description', 'Description', 
								array('placeholder' => 'add description',
									  'class' => 'field-big')); 

		$this -> addElement('text', 'event_site', 'Event web site',
								array('style' => 'display:none;'));

		$this -> addElement('date', 'start_date', 'Start date',
								array('data-format' => 'dd/MM/yyyy',
									  'data-type' => 'event_date',
									  'placeholder' => 'start date'));
		
		$this -> addElement('date', 'start_time', 'Start time',
								array('data-format' => 'hh:mm:ss',
									  'data-type' => 'event_time',
									  'placeholder' => 'start time'));		
		
		$this -> addElement('date', 'end_date', 'End date',
								array('data-format' => 'dd/MM/yyyy',
									  'data-type' => 'event_date',
									  'placeholder' => 'end date'));

		$this -> addElement('date', 'end_time', 'End time',
								array('data-format' => 'hh:mm:ss',
									  'data-type' => 'event_time',
									  'placeholder' => 'end time'));

		$this -> addElement('select', 'event_category', 'Suggest category', 
								array('options' => \Frontend\Models\Category::find(),
									  'using' => array('id', 'name')));
		
		$this -> addElement('hidden', 'event_category_real');		

		$this -> addElement('select', 'campaign_id', 'Campaign', 
								array('options' => $this -> session -> get('member') -> campaign,
									  'using' => array('id', 'name')));
		
		
		$this -> addElement('hidden', 'location_id');
		$this -> addElement('hidden', 'prev_location');
		
		$this -> add(new Submit('Save and publish'));
	}
}