<?php

namespace Frontend\Controllers;

use Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\Category;

/**
 * @RoutePrefix('/')
 */
class SearchController extends \Core\Controller
{

    /**
     * @Route("/search", methods={"POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function searchAction()
    {
        $categories = Category::find();
        $this->view->setVar('categories', $categories->toArray());

        $form = new SearchForm();
        $this -> view -> form = $form;

        if ($this->request->isPost()) {
            $postData = $this->request->getPost();

            $result = array();
            $conditions = array();

            $query = '
                SELECT event.*, category.*, location.*, venue.name AS venue
                FROM \Frontend\Models\Event AS event
                LEFT JOIN \Frontend\Models\EventCategory AS ec ON (event.id = ec.event_id)
                LEFT JOIN \Frontend\Models\Category AS category ON (category.id = ec.category_id)
                LEFT JOIN \Frontend\Models\Location AS location ON (event.location_id = location.id)
                LEFT JOIN \Frontend\Models\Venue AS venue ON (location.id = venue.location_id AND event.fb_creator_uid = venue.fb_uid)
            ';

            $elemExists = function($elem) use (&$postData) {
                if (array_key_exists($elem, $postData) && !is_array($postData[$elem])) {
                    $postData[$elem] = trim(strip_tags($postData[$elem]));
                }
                return (array_key_exists($elem, $postData) && !empty($postData[$elem]));
            };

            if ($elemExists('title')) {
                $conditions[] = 'event.name LIKE "%'.$postData['title'].'%"';
            }

            if ($elemExists('category')) {
                $conditions[] = 'ec.category_id IN ('.implode(',', $postData['category']).')';
            }

            if ($elemExists('location')) {
                $conditions[] = 'location.city LIKE "%'.$postData['locationSearch'].'%"';
            }

            if ($elemExists('start_date')) {
                $conditions[] = 'UNIX_TIMESTAMP(event.start_date) > "'.strtotime($postData['start_dateSearch']).'"';
            }

            if ($elemExists('end_date')) {
                $conditions[] = 'UNIX_TIMESTAMP(event.end_date) < "'.strtotime($postData['end_dateSearch']).'"';
            }

            if (!empty($conditions)) {
                $query .= ' WHERE';
                $count = count($conditions);
                for ($i = 0; $i < $count; $i++) {
                    if ($i !== 0) {$query .= ' AND';}
                    $query .= " ".$conditions[$i];
                }

                $query .= ' GROUP BY event.id';

                $result = $this->modelsManager->executeQuery($query);

                $result->setHydrateMode(Resultset::HYDRATE_ARRAYS);

                $result = $result->toArray();
            }

            $this->view->setVar('result', $result);
        }
    }

}

