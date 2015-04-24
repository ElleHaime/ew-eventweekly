<?php

namespace Frontend\Models;

use Objects\EventImage as EventImageObject;

class EventImage extends EventImageObject
{
	use \Sharding\Core\Env\Phalcon;
	
	
	public function setViewImages($eventId)
	{
		$posters = $flyers = $gallery = $cover = null;
		$result = [];
		
		$this -> setShardById($eventId); 
	 	if ($images = self::find('event_id = "' . $eventId . '"')) {
            foreach ($images as $img) {
                if ($img -> type == 'poster') {
                    $posters[] = $img;
                } else if ($img -> type == 'flyer') {
                    $flyers[] = $img;
                } else if ($img -> type == 'gallery') {
                    $gallery[] = $img;
                } else if ($img -> type == 'cover') {
                    $cover = $img;
                }
            }
		}
		if(isset($posters[0])) { $result['poster'] = $posters; }
		if(isset($flyers[0])) { $result['flyer'] = $flyers; }
		if(isset($cover)) { $result['cover'] = $cover; }
		$result['gallery'] = $gallery;
		
		return $result;
	}

}

