<?php 

namespace Frontend\Models;

use Objects\Category as CategoryObject;

class Category extends CategoryObject
{
    public function getDefaultIdsAsString()
    {
        $result = $this->find('is_default = 1');

        $categories = array();
        foreach ($result as $category) {
            $categories[] = $category -> id;
        }

        return implode(',', $categories);
    }
} 