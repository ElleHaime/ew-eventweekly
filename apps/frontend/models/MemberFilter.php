<?php 

namespace Frontend\Models;

use Objects\MemberFilter as MemberFilterObject,
    Frontend\Models\Category as Category,
	Frontend\Models\Tag as Tag,
	Core\Utils as _U;

class MemberFilter extends MemberFilterObject
{

    protected function beforeSave ()
    {
        if (is_array($this->value)) {
            $this->value = json_encode($this->value);
        }
    }

    public static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    
    public function getbyId($id = false)
    {
        $return = [];

        if (!$id) {
        	if ($this -> getDI() -> has('session') && $this -> getDI() -> get('session') -> has('memberId')) {
        		$id = $this -> getDI() -> get('session') -> get('memberId');
        	} 
        }

        if ($id) {
            $result = MemberFilterObject::find('member_id = '.$id) -> toArray();

            if ($result) {
	            foreach ($result as $node) {
	                if (self::isJson($node['value'])) {
	                    $return[$node['key']]['value'] = json_decode($node['value'], true);
	                }
	                $return[$node['key']]['id'] = $node['id'];
	            }
           }
        }

        return $return;
    }
    
    
    public function compareById($id, $compareSet)
    {
    	$return = $this -> getbyId($id);

    	if (!empty($return)) {
			$isPresetChanged = array_diff($return['category']['value'], $compareSet);

			if (!empty($isPresetChanged)) {
    			$categoryPreseted = $return['category']['value'];
    			$tagsPreseted = $return['tag']['value'];
    			
				$return['category']['value'] = $compareSet;
				$return['tag']['value'] = [];

				foreach ($return['category']['value'] as $node) {
					
					$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\Tag.id 
															FROM Frontend\Models\Tag
															WHERE Frontend\Models\Tag.category_id = " . $node, $this -> getDI());
					$tags = $query -> execute() -> toArray();

					if (in_array($node, $categoryPreseted)) {
						$tagsInSet = [];
						foreach ($tags as $id) {
							$tagsInSet[] = $id['id']; 							
						}
						$intersection = array_intersect($tagsInSet, $tagsPreseted);
						foreach ($intersection as $k => $v) {
							$return['tag']['value'][] = $v;
						}	
					} else {
						foreach ($tags as $tag) {
							$return['tag']['value'][] = $tag['id'];
						}
					}
				}
    		}
    	} 
    	
    	return $return;
    }

} 