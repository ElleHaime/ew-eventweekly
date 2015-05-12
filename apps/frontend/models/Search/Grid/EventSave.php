<?php
/**
 * @namespace
 */
namespace Frontend\Models\Search\Grid;

use
    Engine\Crud\Grid,
    Engine\Crud\Grid\Column,
    Engine\Crud\Grid\Filter as Filter,
    Engine\Crud\Grid\Filter\Field,
    Engine\Filter\SearchFilterInterface as Criteria,
    Engine\Search\Elasticsearch\Filter\AbstractFilter;

/**
 * Class Events.
 *
 * @category   Module
 * @package    Event
 * @subpackage Grid
 */
class EventSave extends Grid
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
     * Initialize grid columns
     *
     * @return void
     */
    protected function _initColumns()
    {
        $this->_columns = [
            'id' => new Column\Primary('Id'),
            'name' => new Column\Text('Name', 'name'),
            'status' => new Column\Collection('Status', 'event_status', [1 => 'Active', 2 => 'Unpublished', 3 => 'Hidden']),
            'member' => new Column\JoinOne("Member", "Frontend\Models\Search\Model\Member"),
            'campaign' => new Column\JoinOne("Campaign", "Frontend\Models\Search\Model\Campaign"),
            'location' => new Column\JoinOne("Location", "Frontend\Models\Search\Model\Location"),
            'venue' => new Column\JoinOne("Venue", "Frontend\Models\Search\Model\Venue"),
            //'category' => new Column\JoinOne("Category", "\Frontend\Models\Search\Model\Category"),
            /*'member' => new Column\Numeric("Member", "member_id"),
            'campaign' => new Column\Numeric("Campaign", "campaign_id"),
            'location' => new Column\Numeric("Location", "location_id"),
            'venue' => new Column\Numeric("Venue", "venue_id"),*/
            'fb_uid' => new Column\Text('Facebook uid', 'fb_uid'),
            'fb_creator_uid' => new Column\Text('Facebook creator uid', 'fb_creator_uid'),
            'description' => new Column\Text('Description', 'description', false),
            'tickets_url' => new Column\Text('tickets_url', 'tickets_url'),
            'start_date' => new Column\Date('Start date', 'start_date'),
            'end_date' => new Column\Date('End date', 'end_date'),
            'recurring' => new Column\Text('recurring', 'recurring'),
            'event_fb_status' => new Column\Text('event_fb_status', 'event_fb_status'),
            'address' => new Column\Text('Address', 'address'),
            'latitude' => new Column\Text('latitude', 'latitude'),
            'longitude' => new Column\Text('longitude', 'longitude'),
            'logo' => new Column\Text('Logo', 'logo'),
            'is_description_full' => new Column\Text('is_description_full', 'is_description_full'),
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
                Criteria::COLUMN_NAME   => Criteria::CRITERIA_LIKE,
                'description'           => Criteria::CRITERIA_LIKE,
                'location'              => Criteria::CRITERIA_LIKE,
                'category'              => Criteria::CRITERIA_LIKE,
                'tag'                   => Criteria::CRITERIA_LIKE,
                'id'          			=> Criteria::CRITERIA_EQ
            ]),
            'id' => new Field\Primary("id"),
            'name' => new Field\Name("Name"),
            'desc' => new Field\Standart("Description", 'description'),
            'location' => new Field\Join("Location", "Frontend\Models\Search\Model\Location"),
            'campaign' => new Field\Join("Campaign", "Frontend\Models\Search\Model\Campaign"),
            'category' => new Field\Join("Category", "Frontend\Models\Search\Model\Category", false, null, ["Frontend\Models\Search\Model\EventCategory", "Frontend\Models\Search\Model\Category"]),
            'member' => new Field\Join("Member", "\Frontend\Models\Search\Model\Member"),
            'tag' => new Field\Join("Tags", "Frontend\Models\Search\Model\Tag", false, null, ["Frontend\Models\Search\Model\EventTag", "Frontend\Models\Search\Model\Tag"]),
            'start_date' => new Field\Date('Event start', null, null, Criteria::CRITERIA_MORE),
            'end_date' => new Field\Date('End start', null, null, Criteria::CRITERIA_LESS),
            'latitude' => new Field\Standart('Latitude', 'latitude', null, Criteria::CRITERIA_EQ),
            'longitude' => new Field\Standart('Longitude', 'longitude', null, Criteria::CRITERIA_EQ),
            'address' => new Field\Standart('Address', 'address', null, Criteria::CRITERIA_LIKE),
            'logo' => new Field\Standart('Logo', 'logo', null, Criteria::CRITERIA_EQ)
        ], null, 'get');


        $this->_filter->getFieldByKey('start_date')->setValueType(AbstractFilter::VALUE_TYPE_DATE);
        $this->_filter->getFieldByKey('end_date')->setValueType(AbstractFilter::VALUE_TYPE_DATE);

        //$tag = $this->_filter->getFieldByKey('tag');
        //$tag->category = "\Frontend\Models\Search\Model\Category";
    }
}
