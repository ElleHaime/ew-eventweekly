<?php

namespace Frontend\Models;

use Objects\VenueImage as VenueImageObject,
 	Core\Utils as _U;

class VenueImage extends VenueImageObject
{
	public function getCover($venue)
	{
		$cover = false;
		$images = self::find(['venue_id = "' . $venue -> id . '" and type = "cover"']);
		
		if (!empty($images)) {
			foreach ($images as $img) {
				$cover = $img;
			}
		}

		return $cover;
	}
	
	
	public function getLogo($event)
	{
		$result = $this -> getDI() -> get('config') -> application -> defaultLogo;
	
		$logoName = $this -> getDI() -> get('config') -> application -> uploadImgDir . 'event/' . $event -> id . '/' . $event -> logo;
		if (isset($event -> logo) && file_exists($logoName)) {
			$result = $this -> getDI() -> get('config') -> application -> relUploadImgDir . 'event/' . $event -> id . '/' . $event -> logo;
		}
	
		return $result;
	}
}

