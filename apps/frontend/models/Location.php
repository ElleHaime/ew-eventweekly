<?php 

namespace Frontend\Models;

use Objects\Location as LocationObject;

class Location extends LocationObject
{
	
	public function resetLocation($lat = null, $lng = null, $city = null)
    {
        $newLocation = $this -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

        if ($newLocation -> id != $this -> session -> get('location') -> id) {
            if (!empty($city)) {
                $newLocation -> city = $city;
                $newLocation -> alias = $city;
            }

            $this -> session -> set('location', $newLocation);
            $this -> session -> set('lastFetchedEvent', 0);
        }

        return $newLocation;
    }
} 
