<?php

namespace Core\Form\Element;

class Html extends \Engine\Form\Element
{
    protected $_html = '';

    public function __construct($name, $attributes = null){
        if (isset($attributes['html'])){
            $this -> _html = $attributes['html'];
            unset($attributes['html']);
        }

        parent::__construct($name, $attributes);
    }

    public function render(){
        return $this -> _html;
    }
}