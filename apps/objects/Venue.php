<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Venue extends Model
{
	public $id;
	public $fb_uid;
	public $fb_username;
	public $eb_uid;
	public $eb_url;
	public $location_id;
	public $name;
	public $address;	
	public $site;
	public $logo;
	public $latitude;  	
	public $longitude;
	public $intro;
	public $description;
	public $worktime;
	public $phone;
	public $email;
	public $transit;
	public $pricerange;
	public $services;
	public $specialties;
	public $payment;
	public $parking;
	
	public $needCache = true;

	
	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('location_id', '\Objects\Location', 'id', array('alias' => 'location',
																	 	   'baseField' => 'alias'));
		$this -> hasMany('id', '\Frontend\Models\Event', 'venue_id', ['alias' => 'event']);
		$this -> hasManyToMany('id', '\Frontend\Models\VenueCategory',
									 'venue_id', 'category_id',
									 '\Frontend\Models\Category', 'id', ['alias' => 'category',
																		 'baseField' => 'name']);
		$this -> hasManyToMany('id', '\Frontend\Models\VenueTag',
									 'venue_id', 'tag_id',
									 '\Frontend\Models\Tag', 'id', ['alias' => 'tag',
																	'baseField' => 'name']);
		$this -> hasMany('id', '\Frontend\Models\VenueImage', 'venue_id', array('alias' => 'image'));
		
	}
	
	public function createOnChange($argument = array())
	{
		$isVenueExists = false;
        $query = '';

		if (!empty($argument)) {
			if (isset($argument['latitude']) && isset($argument['longitude'])) {
				// find by coordinates
				$query = 'latitude = ' . (float)$argument['latitude'] . ' and longitude = ' . (float)$argument['longitude'];
				$isVenueExists = self::findFirst($query);
			} elseif (isset($argument['address']) && !empty($argument['address'])) {
				$query = 'address like "%' . $argument['address'] . '%"';
				$isVenueExists = self::findFirst($query);
			}
		}

		if (!$isVenueExists && $query != '') {
			$this -> assign($argument);
			$this -> save();
			
			return $this;
        }  elseif ($isVenueExists) {
			return  $isVenueExists;
		}  else {
            return  false;
        }
	}

}