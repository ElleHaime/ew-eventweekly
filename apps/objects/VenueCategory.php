<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class VenueCategory extends Model
{
	public $id;
	public $venue_id;
	public $category_id = 1; 
	
	public function initialize()
	{
		parent::initialize();
				
        $this -> belongsTo('venue_id', '\Frontend\Models\Venue', 'id', array('alias' => 'venue_category'));
        $this -> belongsTo('category_id', '\Frontend\Models\Category', 'id', array('alias' => 'venue_category'));
	}
}