<?php

namespace Frontend\Models;

use Objects\EventImage as EventImageObject;

class EventImage extends EventImageObject
{
	use \Sharding\Core\Env\Phalcon;
	
	
	public function setViewImages($eventId)
	{
		$posters = $flyers = $gallery = $cover = null;
		
	 	if ($images = self::find('event_id = ' . $id)) {
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
		
		$this -> view -> setVar('poster', isset($posters[0]) ? $posters[0] : null);
        $this -> view -> setVar('flyer', isset($flyers[0]) ? $flyers[0] : null);
        $this -> view -> setVar('cover', isset($cover) ? $cover : null);
        $this -> view -> setVar('gallery', $gallery);
	}

}