<?php 

namespace Objects;

use Core\Model;

class Classifier extends Model
{
	public $id;
	public $fb_uid;
	public $eb_uid;
	public $name;
    public $is_active;
	
	public function initialize()
	{
		parent::initialize();
				
		//$this -> hasMany('id', '\Models\EventCategory', 'category_id', array('alias' => 'eventpart'));
	}
}