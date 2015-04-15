<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Model;

use Sharding\Core\Env\Phalcon as Sharding;

/**
 * Class event.
 *
 * @category   Module
 * @package    Event
 * @subpackage Model
 */
class EventImage extends \Engine\Mvc\Model
{
	use Sharding;
    /**
     * Default name column
     * @var string
     */
    protected $_nameExpr = 'event_id';

    /**
     * Default order column
     * @var string
     */
    protected $_orderExpr = 'event_id';

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var integer
     */
    public $event_id;
     
    /**
     *
     * @var string
     */
    public $image;
    
    
    /**
     *
     * @var string
     */
    public $type;
    

    /**
     * Initialize method for model.
     */
	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('event_id', '\Frontend\Models\Event', 'id', array('alias' => 'event'));
	}
}
