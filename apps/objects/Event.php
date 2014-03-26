<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Event extends Model
{
	public $id;
	public $fb_uid;
	public $fb_creator_uid;
	public $member_id;
	public $campaign_id;
	public $location_id;
	public $venue_id;
	public $name;
	public $description;
    public $tickets_url;
	public $start_date;
	public $end_date;
	public $recurring;
	public $event_status	= 1;
    public $event_fb_status	= 1;
	public $latitude;
	public $longitude;
	public $address;
	public $logo;
	public $is_description_full = 0;
    public $deleted = 0;
	public $needCache = true;
	public $slugUri;


	public function initialize()
	{
		$this -> belongsTo('venue_id', '\Objects\Venue', 'id', array('alias' => 'venue',
																	 'baseField' => 'name'));
		$this -> belongsTo('location_id', '\Objects\Location', 'id', array('alias' => 'location',
																	 	   'baseField' => 'alias'));
		$this -> hasMany('id', '\Objects\EventImage', 'event_id', array('alias' => 'image'));
		$this -> hasMany('id', '\Objects\EventMember', 'event_id', array('alias' => 'memberpart'));
		$this -> hasMany('id', '\Objects\EventMemberFriend', 'event_id', array('alias' => 'memberfriendpart'));
		$this -> hasMany('id', '\Objects\EventLike', 'event_id', array('alias' => 'memberlike'));
		$this -> hasMany('id', '\Objects\EventSite', 'event_id', array('alias' => 'site'));
		$this -> hasManyToMany('id', '\Objects\EventCategory',
							   'event_id', 'category_id',
							   '\Objects\Category', 'id', array('alias' => 'category',
							   		 							'baseField' => 'name'));
        $this -> hasManyToMany('id', '\Objects\EventTag',
							   'event_id', 'tag_id',
							   '\Objects\Tag', 'id', array('alias' => 'tag',
							   		 							'baseField' => 'name'));
		$this -> hasMany('id', '\Objects\EventLike', 'event_id', array('alias' => 'event_like'));
	}

	
	public function setCache()
	{
		$events = self::find('event_status = 1');
		if ($events) {
			foreach($events as $event) {
				if ($event -> fb_uid && !$this -> getCache() -> exists('fbe_' . $event -> fb_uid)) {
					$this -> getCache() -> save('fbe_' . $event -> fb_uid, $event -> id);
				}
			}
			$this -> getCache() -> save('fb_events', 'cached');
		}
		$this -> getCache() -> save('events_total', $events -> count());
	}
}