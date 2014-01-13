<?php
/**
 * Class ModelConverter
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core\Traits;


trait ModelConverter {

    /**
     * Convert Phalcon data object to array
     *
     * @param $result
     * @return array
     */
    public function resultToArray($result)
    {
        $relations = $this->getModelsManager()->getRelations('Frontend\Models\Event');
        $returnArr = [];
        $i = 0;
        foreach ($result as $event) {
            $returnArr[$i] = $event->toArray();
            foreach ($relations as $relation) {
                $alias = $relation->getOptions()['alias'];
                $relResult = $event->getRelated($alias);
                if ($relResult instanceof Phalcon\Mvc\Model\Resultset) {
                    foreach ($relResult as $model) {
                        $returnArr[$i][$alias][] = $model->toArray();
                    }
                }else {
                    if (is_object($relResult)) {
                        $returnArr[$i][$alias] = $relResult->toArray();
                    }
                }
            }
            $i++;
        }
        return $returnArr;
    }

} 