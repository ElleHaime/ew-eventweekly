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
		parent::initialize();
				
		$this -> belongsTo('venue_id', '\Objects\Venue', 'id', array('alias' => 'venue',
																	 'baseField' => 'name'));
		$this -> belongsTo('location_id', '\Objects\Location', 'id', array('alias' => 'location',
																	 	   'baseField' => 'alias'));
		$this -> belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'event',
																		'baseField' => 'name'));		
		$this -> hasMany('id', '\Frontend\Models\EventImage', 'event_id', array('alias' => 'image'));
		$this -> hasMany('id', '\Frontend\Models\EventMember', 'event_id', array('alias' => 'memberpart'));
		$this -> hasMany('id', '\Frontend\Models\EventMemberFriend', 'event_id', array('alias' => 'memberfriendpart'));
		$this -> hasMany('id', '\Frontend\Models\EventLike', 'event_id', array('alias' => 'event_like'));
		$this -> hasMany('id', '\Frontend\Models\EventSite', 'event_id', array('alias' => 'site'));
		$this -> hasManyToMany('id', '\Frontend\Models\EventCategory',
							   'event_id', 'category_id',
							   '\Frontend\Models\Category', 'id', array('alias' => 'category',
							   		 							'baseField' => 'name'));
        $this -> hasManyToMany('id', '\Frontend\Models\EventTag',
							   'event_id', 'tag_id',
							   '\Frontend\Models\Tag', 'id', array('alias' => 'tag',
							   		 							'baseField' => 'name'));
	}
}
