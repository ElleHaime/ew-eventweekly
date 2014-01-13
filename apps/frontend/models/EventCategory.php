<?php 

namespace Frontend\Models;

use Objects\EventCategory as EventCategoryObject;
use Objects\Category as Categories;

class EventCategory extends EventCategoryObject
{

    public function countEvents()
    {
        $data = array();

        $categories = Categories::find();

        foreach ($categories as $node) {
            $data[$node->id] = $this->count('category_id = '.$node->id);
        }

        return $data;

    }

} 