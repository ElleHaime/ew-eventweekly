<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventRating extends Model
{
	/**
	 * @Primary
	 * @Identity
	 * @Column(type="integer", nullable=false)
	 */
	public $id;
	
	/**
	 * @Column(type="varchar", nullable=false, length=30)
	 */
	public $event_id;
	
	/**
	 * @Column(type="integer")
	 */
	public $location_id;
	
	/**
	 * @Column(type="integer")
	 */
	public $rank;
	
	
	public function initialize()
	{
		parent::initialize();
				
        $this -> hasOne('event_id', '\Objects\Event', 'id', array('alias' => 'event_rating'));
	}
}