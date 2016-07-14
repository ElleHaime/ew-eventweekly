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
class VenueTag extends \Engine\Mvc\Model
{
    /**
     * Default name column
     * @var string
     */
    protected $_nameExpr = 'venue_id';

    /**
     * Default order column
     * @var string
     */
    protected $_orderExpr = 'tag_id';

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var integer
     */
    public $venue_id;
     
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
        $this->belongsTo("venue_id", "\Frontend\Models\Search\Model\Venue", "id", ['alias' => 'Venue']);
        $this->belongsTo("tag_id", "\Frontend\Models\Search\Model\Tag", "id", ['alias' => 'Tag']);
    }
     
}
