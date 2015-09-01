<?php

namespace Frontend\Models;

use Objects\EventImage as EventImageObject,
 	Core\Utils as _U;

class EventImage extends EventImageObject
{
	use \Sharding\Core\Env\Phalcon;
	
	
	public function setViewImages($event)
	{
		$posters = $flyers = $gallery = $cover = null;
		$result = [];

	 	if ($images = $event -> image) {
            foreach ($images as $img) {
                if ($img -> type == 'poster' && !empty($img -> image)) {
                    $posters[] = $img;
                } else if ($img -> type == 'flyer' && !empty($img -> image)) {
                    $flyers[] = $img;
                } else if ($img -> type == 'gallery' && !empty($img -> image)) {
                    $gallery[] = $img;
                } else if ($img -> type == 'cover' && !empty($img -> image)) {
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
	
	public function getCover($eventId)
	{
		$this -> setShardById($eventId);
		return $cover = EventImageObject::findFirst('event_id = "' . $eventId . '" and type = "cover"');
	}

}

