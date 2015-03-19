<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Model;

/**
 * Class Category.
 *
 * @category   Module
 * @package    Event
 * @subpackage Model
 */
class Category extends \Engine\Mvc\Model
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
     * @var string
     */
    public $name;
     
    /**
     *
     * @var integer
     */
    public $parent_id;
     
    /**
     *
     * @var string
     */
    public $key;
     
    /**
     *
     * @var string
     */
    public $is_default;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("parent_id", "Frontend\Models\Search\Model\Category", "id", ['alias' => 'Category']);
        $this->belongsTo("id", "Frontend\Models\Search\Model\Tag", "category_id", ['alias' => 'Category']);
        $this->belongsTo("id", "Frontend\Models\Search\Model\EventCategory", "category_id", ['alias' => 'Event']);
    }
     
}
