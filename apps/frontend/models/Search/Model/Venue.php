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
class Venue extends \Engine\Mvc\Model
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

	public $id;
	public $fb_uid;
	public $fb_username;
	public $eb_uid;
	public $eb_url;
	public $location_id;
	public $name;
	public $address;	
	public $site;
	public $logo;
	public $latitude;  	
	public $longitude;
	public $intro;
	public $description;
	public $worktime;
	public $phone;
	public $email;
	public $transit;
	public $pricerange;
	public $services;
	public $specialties;
	public $payment;
	public $parking;
    

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo("location_id", "\Frontend\Models\Search\Model\Location", "id", ['alias' => 'Location']);
        $this->belongsTo("id", "Frontend\Models\Search\Model\VenueCategory", "venue_id", ['alias' => 'Category']);
        $this->belongsTo("id", "Frontend\Models\Search\Model\VenueTag", "venue_id", ['alias' => 'Tag']);
    }
    
    

    public function getSearchSource()
    {
    	return 'venue';
    }
    
    
    public function onConstruct()
    {
    	$di = $this->getDI();
    	$connections = (array) $di -> get('shardingConfig') -> connections;
    	foreach($connections as $key => $options) {
    		if (!isset($options -> port)) {
    			$options -> port = 3306;
    		}
    		$di->set($key, function () use ($options) {
    			$db = new \Phalcon\Db\Adapter\Pdo\Mysql([
    					"host" => $options->host,
    					"username" => $options->user,
    					"password" => $options->password,
    					"dbname" => $options->database,
    					"port" => $options->port
    			]);
    
    			return $db;
    		});
    	}
    }
    
    
    public function setShardByCriteria($criteria)
    {
    	$criteria = $this -> getSearchSource();
    	$mngr = parent::getModelsManager();
    	$mngr -> setModelSource($this, $criteria);
    	 
    	return;
    }
    
    
    public function getDefaultSorting()
    {
    	return $this -> _orderExpr;
    }
    
}
