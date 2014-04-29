<?php

namespace Objects;

use Core\Model;

class Total extends Model
{
    public $id;
    public $entity;
    public $total;

    public function initialize()
    {
		parent::initialize();
    }
}