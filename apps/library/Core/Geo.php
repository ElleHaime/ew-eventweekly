<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Core\Utils as _U,
	Objects\LocationIp,
	\GeoIp2\WebService\Client;


class Geo extends Plugin
{
	const DEFAULT_RADIUS_DIFF = 10;
	protected $_unitTypes = ['locality',
							  	'colloquial_area',
							  	'sublocality',
								'sublocality_level_5',
								'sublocality_level_4',
								'sublocality_level_3',
								'sublocality_level_2',
								'sublocality_level_1',
								'administrative_area_level_5',
								'administrative_area_level_4',
								'administrative_area_level_3',
								'administrative_area_level_2',
								'administrative_area_level_1',
								'postal_code',								
								'country'];
	
	protected $_apiUrl 	= 'http://maps.googleapis.com/maps/api/geocode/json?';

	protected $_locLonCur				= false;
	protected $_locLatCur				= false;
	protected $_locLonMin				= false;
	protected $_locLatMin				= false;
	protected $_locLonMax 				= false;
	protected $_locLatMax				= false;
	protected $_cityCur 				= false;
	protected $_aliasCur 				= false;
	protected $_countryCur 			= false;
	protected $_stateCur 				= false;
	protected $_countryCode 			= false;	
	protected $_userIp 				= false;
	protected $_config 				= false;
	protected $_isLocationDefault 		= false;
	protected $di 						= false;
	protected $_errors					= array();
	
	
	
	public function __construct($dependencyInjector = null)
	{
		if ($dependencyInjector) {
			$this -> _di = $dependencyInjector;
			$this -> _config = $dependencyInjector -> get('config');
			$this -> _fb_config = $dependencyInjector -> get('facebook_config');
		} else {
			include(CONFIG_SOURCE);
			$this -> _config = json_decode(json_encode($cfg_settings), false);
		} 
		
		$this -> setUserIp();
	}
	
	public function setUserIp()
	{
		if ($this -> _fb_config -> debug) {
			//$this -> _userIp = '195.24.243.10'; 		// Hz gde
			$this -> _userIp = '79.140.3.235'; 			// Odessa
			//$this -> _userIp = '202.29.214.2'; 		// Tai, Hankha
		} else {
			$this -> _userIp = $this -> request -> getClientAddress();
		}
		return;
	}

	
	public function getUserIp()
	{
		return $this -> _userIp;
	}

	public function getFromCache()
	{
		$locExists = LocationIp::findFirst('ip = "' . $this -> _userIp. '"');

		if ($locExists) {
			$location['location_id'] = $locExists -> location_id;			

			return $location;
		} else {
			return false;
		}
	}

	public function setDefaultLocation()
	{
		$dublin = \Frontend\Models\Location::findFirst('city = "Dublin"');
		
		$this -> _locLatCur = ($dublin -> latitudeMax + $dublin -> latitudeMin)/2;
		$this -> _locLonCur = ($dublin -> longitudeMax + $dublin -> longitudeMin)/2;
		$this -> _locLonMin	= $dublin -> longitudeMin;
		$this -> _locLatMin	= $dublin -> latitudeMin;
		$this -> _locLonMax = $dublin -> longitudeMax;
		$this -> _locLatMax = $dublin -> latitudeMax;
		$this -> _cityCur = $dublin -> city;
		$this -> _stateCur = $dublin -> state;
		$this -> _aliasCur = $dublin -> alias;
		$this -> _countryCur = $dublin -> country;
		$this -> _isLocationDefault = true;
		
		$location['ip'] = $this -> _userIp;
		$location['latitude'] = (float)$this -> _locLatCur ;
		$location['longitude'] = (float)$this -> _locLonCur;
		$location['latitudeMin'] = (float)$this -> _locLatMin;
		$location['longitudeMin'] = (float)$this -> _locLonMin;
		$location['latitudeMax'] = (float)$this -> _locLatMax;
		$location['longitudeMax'] = (float)$this -> _locLonMax;
		$location['alias'] = $this -> _aliasCur;
		$location['city'] = $this -> _cityCur;
		$location['state'] = $this -> _stateCur;
		$location['country'] = $this -> _countryCur;
		
		return $location;
	}

	public function setUserLocation()
	{
		try {
	        $client = new Client($this -> _config -> application -> GeoIp2 -> userId, 
	        					 $this -> _config -> application -> GeoIp2 -> licenseKey);
	
	        $record = $client->city($this->_userIp);

	        $this -> _locLatCur = $record->location->latitude;
	        $this -> _locLonCur = $record->location->longitude;
	        $this -> _countryCode = $record->country->isoCode;
	        
	        \Core\Logger::logFile('ips');
	        \Core\Logger::log($this -> _userIp);
	        if (isset($record)) {
	        	\Core\Logger::log($record);
	        }
		} catch (\Exception $e) { 
			$this -> _isLocationDefault = true;
		}
	}

	public function getUserLocation()
	{
		return array('latitude' => $this -> _locLatCur,
					 'longitude' => $this -> _locLonCur);
	}

	
	public function getLocation($coordinates = array())
	{
//_U::dump($coordinates, true);
		$localityScope = [];
		$units = [];
		$baseType = 'locality';
		$location = false;
		
		if (empty($coordinates)) {
			if ($location = $this -> getFromCache()) {
				return $location;
			} else {
				$this -> setUserLocation();
				if ($this -> _isLocationDefault) {
					return $this -> setDefaultLocation();
				}
			}
		}
	
		if (!empty($coordinates)) {
			$url = $this -> _apiUrl . 'components=' . $this -> _buildLocationQuery($coordinates);
	//_U::dump($url, true);
			$result = json_decode(file_get_contents($url));
	//_U::dump($result, true);
			if ($result -> status == 'OK' && count($result -> results) > 0) {
				if (count($result -> results) == 1) {
					$localityScope = $result -> results[0];
					$baseType = 'locality';
				} else {
					foreach ($result -> results as $key => $scope) {
						$isInGeometry = true;
						if (isset($coordinates['latitude']) && isset($coordinates['longitude'])) {
							$isInGeometry = $this -> isInGeometry($coordinates['latitude'], $coordinates['longitude'], $scope -> geometry -> viewport);
						}
						if ($isInGeometry) {
							foreach ($scope -> address_components as $area) {
								if (in_array('locality', $area -> types) && $area -> long_name == $coordinates['city']) {
									$localityScope = $scope;
									$baseType = 'locality';
										
									break;
								}
							}
						}
					}
				}
			}
		}

//_U::dump($localityScope);
		if (empty($localityScope)) 
		{
			if (!empty($coordinates['latitude']) && !empty($coordinates['longitude'])) {
				$queryParams = $this -> _buildCoordinatesQuery($coordinates['latitude'], $coordinates['longitude']);
			} else {
				$queryParams = $this -> _buildCoordinatesQuery($this -> _locLatCur, $this -> _locLonCur, $this -> _countryCode);
			}
			
			$url = $this -> _apiUrl . $queryParams;
			$result = json_decode(file_get_contents($url));
//_U::dump($result);			
			if ($result -> status == 'OK' && count($result -> results) > 0) {
				foreach ($this -> _unitTypes as $index => $type) {
					foreach ($result -> results as $object => $details) {
						if ($details -> types[0] == $type) $units[$details -> types[0]] = $object;
					}
				}
				if (!empty($units)) {
					$firstUnit = array_keys($units)[0];
					$baseType = $firstUnit;
					$localityScope = $result -> results[$units[$firstUnit]];
				} else {
					return false;
				}
			}
		}
// _U::dump($baseType, true);		
// _U::dump($localityScope);
		if (!empty($localityScope))
		{
			foreach ($localityScope -> address_components as $obj => $lvl)
			{
				if (in_array($baseType, $lvl -> types))
				{
					$location['alias'] = $lvl -> long_name;
					$location['city'] = $lvl -> long_name;
					$location['search_alias'] = \Core\Utils\SlugUri::slug($lvl -> long_name);
				}
				if (in_array('administrative_area_level_1', $lvl -> types))
				{
					$location['state'] = $lvl -> long_name;
					if (!isset($location['city']))
					{
						$location['alias'] = $lvl -> long_name;
						$location['city'] = $lvl -> long_name;
					}
				}
				if (in_array('country', $lvl -> types)) $location['country'] = $lvl -> long_name;
			}
			$location['place_id'] = $localityScope -> place_id;
			$location['latitudeMin'] = (float)$localityScope -> geometry -> viewport -> southwest -> lat;
			$location['longitudeMin'] = (float)$localityScope -> geometry -> viewport -> southwest -> lng;
			$location['latitudeMax'] = (float)$localityScope -> geometry -> viewport -> northeast -> lat;
			$location['longitudeMax'] = (float)$localityScope -> geometry -> viewport -> northeast -> lng;
			$location['longitude'] = ($location['longitudeMax'] + $location['longitudeMin'])/2;
			$location['latitude'] = ($location['latitudeMax'] + $location['latitudeMin'])/2;
			
			if (empty($coordinates)) $location['ip'] = $this -> getUserIp();

			$location['resultSet'] = true;
		}
//_U::dump($location);		
		return $location;
	}
	

	protected function _buildLocationQuery($argument = [])
	{
		$result = [];
		
		if ($argument['city']) $result[] = 'locality:' . urlencode($argument['city']);
		if ($argument['country']) $result[] = 'country:' . urlencode($argument['country']);
		if ($argument['administrative_area_level_1']) $result[] = 'administrative_area_level_1:' . urlencode($argument['administrative_area_level_1']); 
		
		return implode('|', $result); 
	}
	
	
	protected function _buildCoordinatesQuery($lat, $lon, $countryCode = false)
	{
		$result = [];

		if ($countryCode) $result[] = 'region=' . $this -> _countryCode;
		if ($lat && $lon) $result[] = 'latlng=' . $lat . ',' . $lon;
		
		return implode("&", $result);
	}

	public function getErrors()
	{
		return $this -> _errors;
	}
	
	
	protected function isInGeometry($lat, $lng, $geometry) 
	{
		$result = false;

		if ($geometry -> northeast -> lat >= $lat && $geometry -> southwest -> lat <= $lat &&
			$geometry -> northeast -> lng >= $lng && $geometry -> southwest -> lng <= $lng) 
			$result = true;
		
		return $result;
	}
}
