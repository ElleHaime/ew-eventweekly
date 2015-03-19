<?php
/**
 * Class ModelConverter
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core\Traits;

use Core\Utils as _U;

trait ModelConverter {
    /**
     * Convert Phalcon data object to array
     *
     * @param $result
     * @return array
     */
    public function resultToArray($result)
    {
    	
        $relations = $this->getModelsManager()->getRelations((string)__CLASS__);
        $relationsManyToMany = $this->getModelsManager()->getHasManyToMany($this);
        $returnArr = [];
        $i = 0;
        
        foreach ($result as $event) {
            $returnArr[$i] = $event->toArray();

            if (isset($event->virtualFields) && is_array($event->virtualFields) && !empty($event->virtualFields)) {
                foreach ($event->virtualFields as $key => $code) {
                    $code = preg_replace('/self/', '$event', $code);
                    $returnArr[$i][$key] = eval('return '.$code.';');
                }
            }
            
            $getRelations = function($relations) use (&$returnArr, $event, $i) {
                foreach ($relations as $relation) {
                	$alias = $relation -> getOptions()['alias'];

                	if ($alias != 'site') {
						$relResult = $event -> $alias;
						if ($relResult instanceof Phalcon\Mvc\Model\Resultset) {
							foreach ($relResult as $model) {
								$returnArr[$i][$alias][] = $model -> toArray();
							}
						} else {
							if (is_object($relResult)) {
								$returnArr[$i][$alias] = $relResult -> toArray();
							}
						}
                	}
                }
            };
            $getRelations($relations);
            $getRelations($relationsManyToMany);
            $i++;
        }

        return $returnArr;
    }

} 