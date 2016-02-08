<?php 

namespace Frontend\Models;

use Objects\Location as LocationObject;

class Location extends LocationObject
{
	public function resetLocation($lat = null, $lng = null, $city = null)
    {
    	$session = $this -> getDI() -> get('session');
        $newLocation = $this -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

        if ($newLocation -> id != $session -> get('location') -> id) {
            if (!empty($city)) {
                $newLocation -> city = $city;
                $newLocation -> alias = $city;
            }

            $session -> set('location', $newLocation);
            $session -> set('lastFetchedEvent', 0);
        }

        return $newLocation;
    }
    
    
    public function wasChanged($city, $country)
    {
    	$session = $this -> getDI() -> get('session');
    }
} 
