<?php

namespace Core\Form\Element;

class Radio extends \Phalcon\Forms\Element\Select
{
    public function __construct($name, $options = null, $attributes = null)
    {
        $optionsData = (!empty($options['options']) ? $options['options'] : null);
        unset($options['options']);
        if (!is_array($attributes)) {
            $attributes = array();
        }
        
        $options = array_merge($options, $attributes);
        
        parent::__construct($name, $optionsData, $options);
    }

    public function render($attributes = null)
    {
        $content = '';
        $options = $this -> getOptions();
        $attributes = $this -> getAttributes();
        $name = $this -> getName();
        $value = (isset($attributes['value']) ? $attributes['value'] : null);

        if (is_array($options)) {
            foreach ($options as $key => $option) {
                $content .= sprintf('<label class="radio"><input type="radio" value="%s" %s name="%s" id="%s">%s</label>',
                    $key,
                    ($key == $value ? 'checked="checked"' : ''),
                	$name,
                	$name,
                    $option
                );
            }
        } else {
            if (!isset($attributes['using']) || !is_array($attributes['using']) || count($attributes['using']) != 2)
                throw new \Exception("The 'using' parameter is required to be an array with 2 values.");
            $keyAttribute = array_shift($attributes['using']);
            $valueAttribute = array_shift($attributes['using']);
            foreach ($options as $option) {
                $optionKey = $option -> readAttribute($keyAttribute);
                $optionValue = $option -> readAttribute($valueAttribute);
                $content .= sprintf('<label class="radio"><input type="radio" value="%s" %s name="%s" id="%s">%s</label>',
                    $optionKey,
                    ($optionKey == $value ? 'checked="checked"' : ''),
                	$name,
                	$name,
                    $optionValue
                );
            }
        }

        return $content;
    }
}