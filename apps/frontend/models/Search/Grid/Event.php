<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Grid;

use
    Engine\Crud\Grid,
    Engine\Crud\Grid\Column,
    Engine\Crud\Grid\Filter\Search\Elasticsearch as Filter,
    Engine\Crud\Grid\Filter\Field,
    Engine\Filter\SearchFilterInterface as Criteria;

/**
 * Class Events.
 *
 * @category   Module
 * @package    Event
 * @subpackage Grid
 */
class Event extends Grid
{
    /**
     * Container adapter class name
     * @var string
     */
    protected $_containerAdapter = 'Mysql\Elasticsearch';

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
        $this->_columns = [
            'id' => new Column\Primary('Id'),
            /*'location' => new Column\JoinOne("Location", "\Frontend\Models\Search\Model\Location"),
            'category' => new Column\JoinMany("Category", ["\Frontend\Models\Search\Model\EventCategory", "\Frontend\Models\Search\Model\Category"]),
            'name' => new Column\Text('Name', 'name'),
            'status' => new Column\Collection('Status', 'event_status', [1 => 'Active', 2 => 'Unpublished', 3 => 'Hidden']),
            'member' => new Column\JoinOne("Member", "\Frontend\Models\Search\Model\Member"),
            //'campaign' => new Column\JoinOne("Campaign", "\Frontend\Models\Search\Model\Campaign"),
            'tags' => new Column\JoinMany("Tags", ["\Frontend\Models\Search\Model\EventTag", "\Frontend\Models\Search\Model\Tag"]),
            'venue' => new Column\JoinOne("Venue", "\Frontend\Models\Search\Model\Venue"),
            'description' => new Column\Text('Description', 'description', false),
            /*'member' => new Column\Numeric("Member", "member_id"),
            'campaign' => new Column\Numeric("Campaign", "campaign_id"),
            'location' => new Column\Numeric("Location", "location_id"),
            'venue' => new Column\Numeric("Venue", "venue_id"),*/
            /*'fb_uid' => new Column\Text('Facebook uid', 'fb_uid'),
            'fb_creator_uid' => new Column\Text('Facebook creator uid', 'fb_creator_uid'),
            'tickets_url' => new Column\Text('tickets_url', 'tickets_url'),
            'start_date' => new Column\Date('Start date', 'start_date'),
            'end_date' => new Column\Date('End date', 'end_date'),
            'recurring' => new Column\Text('recurring', 'recurring'),
            'event_fb_status' => new Column\Text('event_fb_status', 'event_fb_status'),
            'address' => new Column\Text('Address', 'address'),
            'latitude' => new Column\Text('latitude', 'latitude'),
            'longitude' => new Column\Text('longitude', 'longitude'),
            'logo' => new Column\Text('Logo', 'logo'),
            'is_description_full' => new Column\Text('is_description_full', 'is_description_full'),*/
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
                'description'   => Criteria::CRITERIA_BEGINS,
            ], null, 280, null, 255, false),
            'searchLocationField' => new Field\Join("Location", "\Frontend\Models\Search\Model\Location"),
            'searchCategory' => new Field\Join("Category", "\Frontend\Models\Search\Model\Category", false, null, ["\Frontend\Models\Search\Model\EventCategory", "\Frontend\Models\Search\Model\Category"]),
            'searchTitle' => new Field\Name("Name"),
            'searchTag' => new Field\Join("Tags", "\Frontend\Models\Search\Model\Tag", false, null, ["\Frontend\Models\Search\Model\EventTag", "\Frontend\Models\Search\Model\Tag"]),
            'searchStartDate' => new Field\Date('Event start', null, null, Criteria::CRITERIA_MORE)
        ], null, 'get');

        //$tag = $this->_filter->getFieldByKey('tag');
        //$tag->category = "\Frontend\Models\Search\Model\Category";
    }

    /**
     * Setup container
     *
     * @return void
     */
    protected function _setupContainer()
    {
        $this->_container->useIndexData();
    }
}
