<?php

namespace Core\Form\Element;

class Check extends \Phalcon\Forms\Element\Check
{

	public function __construct($name, $attributes = null){
        if (isset($attributes['value']) && $attributes['value'] == true){
            $attributes['checked'] = 'checked';
        }

        if (isset($attributes['options'])){
            $attributes['value'] = $attributes['options'];
            unset($attributes['options']);
        }

        parent::__construct($name, $attributes);
    } 

    public function setDefault($value)
    {
        if ($value == true) {
            $this -> setAttribute('checked', 'checked');
        } else {
            $attributes = $this -> getAttributes();
            unset($attributes['checked']);
            $this -> setAttributes($attributes);
        }

        parent::setDefault($value);
    } 
}