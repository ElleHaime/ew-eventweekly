<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class VenueImage extends Model
{
	public $id;
	public $venue_id;
	public $image;
	public $type;

	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('venue_id', '\Frontend\Models\Venue', 'id', array('alias' => 'venue'));
	}
}