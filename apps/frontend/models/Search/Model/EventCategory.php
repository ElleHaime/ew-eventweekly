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
class EventCategory extends \Engine\Mvc\Model
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
    protected $_orderExpr = 'category_id';

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
     * @var integer
     */
    public $category_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("event_id", "\Frontend\Models\Search\Model\Event", "id", ['alias' => 'Event']);
        $this->belongsTo("category_id", "\Frontend\Models\Search\Model\Category", "id", ['alias' => 'Category']);
    }
     
}
