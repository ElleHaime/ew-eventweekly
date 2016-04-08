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
class Location extends \Engine\Mvc\Model
{
    /**
     * Default name column
     * @var string
     */
    protected $_nameExpr = 'city';

    /**
     * Default order column
     * @var string
     */
    protected $_orderExpr = 'city';

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
    public $facebook_id;
     
    /**
     *
     * @var string
     */
    public $city;
     
    /**
     *
     * @var string
     */
    public $state;
     
    /**
     *
     * @var string
     */
    public $country;
     
    /**
     *
     * @var string
     */
    public $alias;
     
    /**
     *
     * @var string
     */
    public $place_id;
     
    /**
     *
     * @var string
     */
    public $cordinates;
     
    /**
     *
     * @var double
     */
    public $latitudeMin;
     
    /**
     *
     * @var double
     */
    public $longitudeMin;
     
    /**
     *
     * @var double
     */
    public $latitudeMax;
     
    /**
     *
     * @var double
     */
    public $longitudeMax;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("parent_id", "\Frontend\Models\Search\Model\Location", "id", ['alias' => 'Location']);
    }
}
