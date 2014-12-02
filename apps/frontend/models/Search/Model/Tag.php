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
class Tag extends \Engine\Mvc\Model
{
    /**
     * Default name column
     * @var string
     */
    protected $_nameExpr = 'name';

    /**
     * Default order column
     * @var string
     */
    protected $_orderExpr = 'name';

    /**
     * Order is asc order direction
     * @var bool
     */
    protected $_orderAsc = true;

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var integer
     */
    public $category_id;
     
    /**
     *
     * @var string
     */
    public $name;
     
    /**
     *
     * @var string
     */
    public $key;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("category_id", "\Frontend\Models\Search\Model\Category", "id", ['alias' => 'Category']);
        $this->belongsTo("tag_id", "Frontend\Models\Search\Model\EventTag", "id", ['alias' => 'Event']);
    }
}
