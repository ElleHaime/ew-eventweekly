<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Category extends Model
{
	public $id;
	public $key;
	public $name;
    public $parent_id;
    public $is_default;
	
	public function initialize()
	{
		parent::initialize();
		$this -> hasMany('id', '\Frontend\Models\EventCategory', 'category_id', array('alias' => 'eventpart'));
	}
}