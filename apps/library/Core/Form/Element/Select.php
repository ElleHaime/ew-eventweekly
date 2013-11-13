<?php

namespace Core\Form\Element;

class Select extends \Phalcon\Forms\Element\Select
{
    public function __construct($name, $options = null, $attributes = null){
        $optionsData = (!empty($options['options']) ? $options['options'] : null);
        unset($options['options']);
        
        if (!is_array($attributes)) {
            $attributes = array();
        }
        
        $options = array_merge($options, $attributes);
        
        parent::__construct($name, $optionsData, $options);
    }
}