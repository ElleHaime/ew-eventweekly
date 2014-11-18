<?php 

namespace Frontend\Models;

use Objects\EventCategory as EventCategoryObject;
use Objects\Category as Categories;

class EventCategory extends EventCategoryObject
{
//	use \Sharding\Core\Env\Phalcon;
	
    public function countEvents()
    {
        $data = array();

        $session = $this->getDI()->getShared('session');
        if ($session->has('countEventsInCats')) {
            $data = $session->get('countEventsInCats');
        }

        if (empty($data)) {
            $categories = Categories::find();

            foreach ($categories as $node) {
                $data[$node->id] = $this->count('category_id = '.$node->id);
            }

            $session->set('countEventsInCats', $data);
        }
        return $data;

    }

} 