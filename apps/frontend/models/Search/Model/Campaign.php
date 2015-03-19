<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Model;

/**
 * Class Campaign.
 *
 * @category   Module
 * @package    Event
 * @subpackage Model
 */
class Campaign extends \Engine\Mvc\Model
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
    protected $_orderExpr = 'location_id';

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var integer
     */
    public $member_id;
     
    /**
     *
     * @var string
     */
    public $name;
     
    /**
     *
     * @var string
     */
    public $description;
     
    /**
     *
     * @var string
     */
    public $logo;
     
    /**
     *
     * @var string
     */
    public $address;
     
    /**
     *
     * @var integer
     */
    public $location_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("member_id", "\Frontend\Models\Search\Model\Member", "id", ['alias' => 'Member']);
        $this->belongsTo("location_id", "\Frontend\Models\Search\Model\Location", "id", ['alias' => 'Location']);
    }
}
