<?php
/**
 * Class CoreTag
 *
 * @author   Slava Basko <basko.slava@gmail.com>
 */

namespace Core;

class CoreTag extends \Phalcon\Tag {

    static $config = null;

    public function __construct($config = null)
    {
        self::$config = $config;
    }

    public static function stylesheetLink($parameters = null, $local = null)
    {
        if (is_string($parameters) && self::$config->application->debug) {
            $parameters = $parameters.'?'.rand(0, 9999999);
        }
        return parent::stylesheetLink($parameters, $local);
    }

    public static function javascriptInclude($parameters = null, $local = null)
    {
        if (is_string($parameters) && self::$config->application->debug) {
            $parameters = $parameters.'?'.rand(0, 9999999);
        }
        return parent::javascriptInclude($parameters, $local);
    }

    public static function checkLogo($args = [], $type = 'event')
    {
    	$result = '';
    	$args = (array)$args;
    	
		if ($type == 'event') {
    		$fPath = self::composeEventPath($args);
    			
    		(!is_null($args['logo']) && file_exists($fPath['full'] . '/' . $args['logo']))
    		? $result = $fPath['rel'] . '/' . $args['logo']
    		: $result = '/img/logo200.png';
		} else {
			$fPath = self::composeVenuePath($args);
			
			(!is_null($args['logo']) && file_exists($fPath['full'] . '/' . $args['logo'])) 
				? $result = $fPath['rel'] . '/' . $args['logo'] 
				: $result = '/img/logo201.png';
		}
		
		return $result;
    }
    
    public static function checkCover($args = [], $cover = null, $type = 'event')
    {
    	$result = '';
    	$args = (array)$args;
    	 
    	if ($type == 'event') {
    		$fPath = self::composeEventPath($args);
    			
    		(!is_null($cover) && file_exists($fPath['full'] . '/cover/' . $cover))
	    		? $result = $fPath['rel'] . '/cover/' . $cover
	    		: $result = '/img/logo200.png';
    	} else {
    		$fPath = self::composeVenuePath($args);
    		
    		(!is_null($args['logo']) && file_exists($fPath['full'] . '/cover/' . $cover))
	    		? $result = $fPath['rel'] . '/cover/' . $args['logo']
	    		: $result = '/img/logo201.png';
    	}
    
    	return $result;
    }
    
    private static function composeEventPath($args)
    {
    	if(!empty($args['start_date'])) {
    		$objDatesName = date('Y', strtotime($args['start_date'])) . '/'
    				. date('m', strtotime($args['start_date'])) . '/'
    						. date('d', strtotime($args['start_date']));
    	} else {
    		$objDatesName = 'undated';
    	}
    	
    	return ['full' => ROOT_APP . 'public/upload/img/event/' . $objDatesName. '/' . $args['id'],
    			 'rel' => '/upload/img/event/' . $objDatesName. '/' . $args['id']];
    }

    private static function composeVenuePath($args)
    {
    	return ROOT_APP . 'public/upload/img/venue/' . $args['location_id'] . '/' . $args['id'];
    }
} 