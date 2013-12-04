<?php 

namespace Objects;

use Core\Model;

class Category extends Model
{
	public $id;

	public $key;

	public $name;

    public $parent_id;
	
	public function initialize()
	{
        $this->hasMany('id', '\Objects\EventCategory', 'category_id');
	}
}