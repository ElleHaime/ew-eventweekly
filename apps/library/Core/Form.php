<?php

namespace Core;

use Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Identical,
	Phalcon\Validation\Validator\StringLength,
	Core\Utils as _U;


class Form extends \Phalcon\Forms\Form
{
	const METHOD_DELETE = 'delete';
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const METHOD_PUT = 'put';

	const ENCTYPE_MULTIPART = 'multipart-formdata';
	const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';

	protected $_elementsData = array();
	private $_errors = array();
	private $_hasValidators = false;
	private $_isValid = false;
	private $_elementsOptions = array('label',
									  'description',
									  'filters',
									  'required',
									  'validators');

	public function __construct(\Phalcon\Mvc\Model $entity = null)
	{
		parent::__construct($entity);
		$this -> init();
	}

	public function init()
	{
	}

	public function addElement($type, $name, $label = '', $options = array())
	{
		$elementClass = '\Core\Form\Element\\' . ucfirst($type);
		if (!class_exists($elementClass))
			throw new \Exception("Element with type '{$type}' doesn't exist.");
		
		if ($type == "file") {
			$this -> _enctype = self::ENCTYPE_MULTIPART;
		}
		
		$params = array_intersect_key($options, array_flip($this -> _elementsOptions));
		$attributes = array_diff_key($options, $params);

		if (!empty($attributes)) {
			$element = new $elementClass($name, $attributes);
		} else {
			$element = new $elementClass($name);
		}

		$element -> setLabel($label);
		
		if ($this -> _entity && isset($this -> _entity -> $name)){
			$element -> setDefault($this -> _entity -> $name);
		}
		
		if (!empty($params['validators'])) {
			$this -> _hasValidators = true;
			$validators = array();
			
			foreach ($params['validators'] as $vldType => $vldOptions) {
				$itemClass = '\Phalcon\Validation\Validator\\'  . $vldType;
				$validators[] = new $itemClass($vldOptions);
			}
			$element -> addValidators($validators);
		}
	
		$this -> _elementsData[$name] = array('type' => $type,
										  	  'element' => $element,
										  	  'params' => $params,
										  	  'attributes' => $attributes);
		$this -> add($element);
		
		return $this;
	}
	
	public function addButton($name, $is_submit = false, $params = array())
	{
	}

	public function addButtonAction($name, $jsAction)
	{
	}

	public function setOption($name, $value)
	{
	}

	public function removeOption($name)
	{
	}

	public function setAttribute($name, $value)
	{
	}

	public function removeAttribute($name)
	{
	}

	public function getElement($name)
	{
	}

	public function removeElement($name)
	{
	}
	
	public function addError($message)
	{
		$this -> _errors[] = $message;
		return $this;
	}	
	
	public function isValid($data = null, $entity = null)
	{
		$elementsData = $this -> _elementsData;
		$this -> setFormValues($data);

		if (parent::isValid($data)) {
			$this -> _isValid = true;
		} 
		
		return $this -> _isValid; 
	}
	
	
	public function setFormValues($data) 
	{
		foreach ($this -> _elementsData as $elem => $value) {
			if (isset($data[$elem])) {
				$value['element'] -> setDefault($data[$elem]);
				$this -> _elementsData[$elem]['attributes']['value'] = $data[$elem];
			} else {
				$value['element'] -> setDefault(null);
			}
		}
		
		return $this;
	}
	
	
	public function getFormValues($getEntity = true)
	{
		$values = array();
		
		foreach ($this -> _elementsData as $elem => $value) {
			if ($this -> request -> hasPost($elem) && isset($this -> _elementsData[$elem]['attributes']['value'])) {
				$values[$elem] = $this -> _elementsData[$elem]['attributes']['value']; 
			} else {
				$values[$elem] = null; 
			}
		} 
		
		return $values;
	} 
	
	public function clearElements()
	{
		$this -> _elementsData = array();
	}
	
	public function messages($name)
	{
		if ($this -> hasMessagesFor($name)) {
			foreach($this -> getMessagesFor($name) as $message) {
				$this -> flash -> error($message);
			}
		}
	}
}