<?php

namespace Frontend\Controllers;

use Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Core\Utils as _U;

/**
 * @RoutePrefix('/')
 */
class SearchController extends \Core\Controller
{

    /**
     * @Route("/search", methods={"GET", "POST"})
     * @Route("/search/list", methods={"GET", "POST"})
     * @Route("/search/map", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function searchAction()
    {
        $categories = Category::find();
        $categories = $categories->toArray();
        $this->view->setVar('categories', $categories);

        $form = new SearchForm();
        $this -> view -> form = $form;

        $result = array();
        $countResults = 0;
        $Event = new Event();
        $postData = $this->request->getPost();

        if (empty($postData)) {
            $postData = $this->session->get('userSearch');
        }

        $elemExists = function($elem, $empty = true) use (&$postData) {
            if (array_key_exists($elem, $postData) && !is_array($postData[$elem])) {
                $postData[$elem] = trim(strip_tags($postData[$elem]));
            }

            if ($empty === true) {
                $answer = (array_key_exists($elem, $postData) && !empty($postData[$elem]));
            }else {
                $answer = (array_key_exists($elem, $postData) && empty($postData[$elem]));
            }
            return $answer;
        };

        if ($elemExists('searchLocationLatMin', false) || $elemExists('searchLocationLatMax', false) || $elemExists('searchLocationLngMin', false) || $elemExists('searchLocationLngMax', false)) {
            $location = $this->session->get('location');
            $postData['searchLocationLatMin'] = $location->latitudeMin;
            $postData['searchLocationLatMax'] = $location->latitudeMax;
            $postData['searchLocationLngMin'] = $location->longitudeMin;
            $postData['searchLocationLngMax'] = $location->longitudeMax;
        }

        $pageTitle = 'Search results: ';

        if (!empty($postData)) {
            $this->view->setVar('userSearch', $postData);


            if ($elemExists('searchTitle')) {
                $Event->addCondition('Frontend\Models\Event.name LIKE "%'.$postData['searchTitle'].'%"');

                $pageTitle .= 'by title - "'.$postData['searchTitle'].'" | ';
            }

            if ($elemExists('searchCategory')) {
                $Event->addCondition('Frontend\Models\EventCategory.category_id IN ('.implode(',', $postData['searchCategory']).')');

                $pageTitle .= 'by categories - ';

                foreach ($categories as $node) {
                    if (in_array($node['id'], $postData['searchCategory'])) {
                        $pageTitle .= ' '.$node['name'];
                    }
                }

                if (count($postData['searchCategory']) == 1) {
                    $this->view->setVar('primaryCategory', $postData['searchCategory'][0]);
                }

            }

            if ($elemExists('searchLocationLatMin') && $elemExists('searchLocationLatMax') && $elemExists('searchLocationLngMin') && $elemExists('searchLocationLngMax') && (($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'private') || ($elemExists('searchLocationField') && $postData['searchLocationField'] != ''))) {
                $Event->addCondition('Frontend\Models\Event.latitude BETWEEN '.$postData['searchLocationLatMin'].' AND '.$postData['searchLocationLatMax'].' AND Frontend\Models\Event.longitude BETWEEN '.$postData['searchLocationLngMin'].' AND '.$postData['searchLocationLngMax']);

                $lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
                $lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;

                $loc = new Location();
                $newLocation = $loc -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

                $this->session->set('location', $newLocation);

                $this->cookies->get('lastLat')->delete();
                $this->cookies->get('lastLng')->delete();
            }

            if ($elemExists('searchStartDate')) {
                $Event->addCondition('Frontend\Models\Event.start_date > "'.$postData['searchStartDate'].'"');
            }

            if ($elemExists('searchEndDate')) {
                $Event->addCondition('Frontend\Models\Event.end_date < "'.$postData['searchEndDate'].'"');
            }else {
                $Event->addCondition('Frontend\Models\Event.end_date > "'.date('Y-m-d H:m:i', time()).'"');
            }

            if ($elemExists('searchType')) {
                if ($postData['searchType'] == 'in_map') {
                    $result = $Event->fetchEvents(Event::FETCH_ARRAY);
                    $countResults = count($result);
                    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
                }else {
                    $page = $this->request->getQuery('page');
                    if (empty($page)) {
                        $page = 1;
                    }
                    $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10]);

                    $result = $fetchedData->items;

                    unset($fetchedData->items);

                    $countResults = $fetchedData->total_items;
                }
            }

        }

        if ($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'global') {
            $this->session->set('userSearch', $postData);
        }

        $this->view->setVar('list', $result);
        $this->view->setVar('eventsTotal', $countResults);
        if (isset($fetchedData)) {
            $this->view->setVar('pagination', $fetchedData);
        }

        $this->view->setVar('listTitle', $pageTitle);

        if ($postData['searchType'] == 'in_map') {
            $this->view->pick('event/mapEvent');
        } else {
            $this->view->pick('event/eventList');
        }
    }

}

