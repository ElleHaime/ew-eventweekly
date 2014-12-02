<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Model;

/**
 * Class event.
 *
 * @category   Module
 * @package    Event
 * @subpackage Model
 */
class EventTag extends \Engine\Mvc\Model
{
    /**
     * Default name column
     * @var string
     */
    protected $_nameExpr = 'tag_id';

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
     * @var integer
     */
    public $tag_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("event_id", "Frontend\Models\Search\Model\Event", "id", ['alias' => 'Event']);
        $this->belongsTo("tag_id", "Frontend\Models\Search\Model\Tag", "id", ['alias' => 'Tag']);
    }
}
