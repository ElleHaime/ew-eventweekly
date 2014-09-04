<?php 

namespace Frontend\Models;

use Objects\Venue as VenueObject;

class Venue extends VenueObject
{
	use \Sharding\Core\Env\Phalcon;
} 