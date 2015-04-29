<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Grid;

use
    Engine\Crud\Grid,
    Engine\Crud\Grid\Column,
    //Engine\Crud\Grid\Filter\Search\Elasticsearch as Filter,
    Engine\Crud\Grid\Filter as Filter,    
    Engine\Crud\Grid\Filter\Field,
    Engine\Filter\SearchFilterInterface as Criteria;

/**
 * Class Events.
 *
 * @category   Module
 * @package    Event
 * @subpackage Grid
 */
class EventBase extends Grid
{
    /**
     * Container adapter class name
     * @var string
     */
    protected $_containerAdapter = 'Mysql';

    /**
     * Grid title
     * @var string
     */
    protected $_title = 'Event';

    /**
     * Container model
     * @var string
     */
    protected $_containerModel = '\Frontend\Models\Search\Model\Event';

    /**
     * Container condition
     * @var array|string
     */
    protected $_containerConditions = null;

    /**
     * Default grid params
     * @var array
     */
    protected $_defaultParams = [
        'sort' => false,
        'direction' => false,
        'page' => 1,
        'limit' => 10
    ];

    /**
     * Initialize grid columns
     *
     * @return void
     */
    protected function _initColumns()
    {
        $this->setStrictMode(false);

        $this->_columns = [
            'id' => new Column\Primary('Id'),
            'name' => new Column\Text('Name', 'name'),
            //'status' => new Column\Collection('Status', 'event_status', [1 => 'Active', 2 => 'Unpublished', 3 => 'Hidden']),
            'member' => new Column\JoinOne("Member", "\Frontend\Models\Search\Model\Member"),
            //'campaign' => new Column\JoinOne("Campaign", "\Event\Model\Campaign"),
            //'location' => new Column\JoinOne("Location", "\Event\Model\Location"),
            //'venue' => new Column\JoinOne("Venue", "\Event\Model\Venue"),
            //'category' => new Column\JoinOne("Category", "\Event\Model\Category"),
            'member_id' => new Column\Text("Member", "member_id"),
            'campaign' => new Column\Text("Campaign", "campaign_id"),
            'location' => new Column\Text("Location", "location_id"),
            //'venue' => new Column\Numeric("Venue", "venue_id"),
            'fb_uid' => new Column\Text('Facebook uid', 'fb_uid'),
            'fb_creator_uid' => new Column\Text('Facebook creator uid', 'fb_creator_uid'),
            'description' => new Column\Text('Description', 'description', false),
            'tickets_url' => new Column\Text('tickets_url', 'tickets_url'),
            'start_date' => new Column\Date('Start date', 'start_date'),
            'end_date' => new Column\Date('End date', 'end_date'),
            'recurring' => new Column\Text('recurring', 'recurring'),
            'event_status' => new Column\Text('event_status', 'event_status'),
            'event_fb_status' => new Column\Text('event_fb_status', 'event_fb_status'),
            'address' => new Column\Text('Address', 'address'),
            'latitude' => new Column\Text('latitude', 'latitude'),
            'longitude' => new Column\Text('longitude', 'longitude'),
            'logo' => new Column\Text('Logo', 'logo')
        
        ];
    }
    
    /**
     * Initialize grid filters
     *
     * @return void
     */
    protected function _initFilters()
    {
        $this->_filter = new Filter([
            'search' => new Field\Search('Search', 'search', [
                'location'      => Criteria::CRITERIA_LIKE,
                'tag'           => Criteria::CRITERIA_LIKE,
                'category'      => Criteria::CRITERIA_LIKE,
                'name'          => Criteria::CRITERIA_BEGINS,
                'id'          	=> Criteria::CRITERIA_EQ,
                'description'   => Criteria::CRITERIA_BEGINS,
            	'member'		=> Criteria::CRITERIA_EQ,
            ], null, 280, null, 255, false),
            'searchLocationField' => new Field\Join("Location", "\Frontend\Models\Search\Model\Location"),
            'searchCategory' => new Field\Join("Category", "\Frontend\Models\Search\Model\Category", false, null, ["\Frontend\Models\Search\Model\EventCategory", "\Frontend\Models\Search\Model\Category"]),
            'searchTitle' => new Field\Name("Name"),
            //'searchId' => new Field\Primary("Id", 'id', null, Criteria::CRITERIA_IN),
        	'searchId' => new Field\Primary("Id", 'id', Criteria::CRITERIA_IN),
        	'searchNotId' => new Field\Standart("Id", 'id', Criteria::CRITERIA_NOTIN),
            'searchMember' => new Field\Standart('MemberI', 'member_id', null, Criteria::CRITERIA_EQ),
            'searchDesc' => new Field\Standart("Desc", "description"),
            'searchTag' => new Field\Join("Tags", "\Frontend\Models\Search\Model\Tag", false, null, ["\Frontend\Models\Search\Model\EventTag", "\Frontend\Models\Search\Model\Tag"]),
            'searchStartDate' => new Field\Date('Event start', 'start_date', null, Criteria::CRITERIA_MORE),
            'searchEndDate' => new Field\Date('End start', 'end_date', null, Criteria::CRITERIA_LESS),
        	'searchLatitude' => new Field\Standart('Latitude', 'latitude', null),
        	'searchLongitude' => new Field\Standart('Longitude', 'longitude', null),
        	'searchAddress' => new Field\Standart('Address', 'address', null, Criteria::CRITERIA_LIKE),
        	'searchStatus' => new Field\Standart('Status', 'event_status', null, Criteria::CRITERIA_EQ),
        	'searchLogo' => new Field\Standart('Logo', 'logo')
        ], null, 'get');
    }
}
