<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Category extends Model
{
	public $id;
	public $name;
	public $parent_id = 0; 


	public function initialize()
	{
		$this -> hasMany('id', '\Objects\EventCategory', 'category_id', array('alias' => 'eventpart'));
	}
}