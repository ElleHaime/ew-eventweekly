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
    	if ($this->session->has('user_token') && $this->session->has('user_fb_uid')) {
            $newTask = null;

            $taskSetted = \Objects\Cron::find(array('member_id = ' . $this -> session -> get('memberId')));
            if ($taskSetted -> count() > 0) {
                foreach ($taskSetted as $task) {
                    $tsk = $task;
                }
                if (time()-($tsk -> hash) > $this -> config -> application -> pingFbPeriod) {
                    $newTask = $tsk;
                }
            } else {
                $newTask = new \Objects\Cron();
            }

            if (!is_null($newTask)) {
                $params = ['user_token' => $this -> session -> get('user_token'),
                           'user_fb_uid' => $this -> session -> get('user_fb_uid'),
                           'member_id' => $this -> session -> get('memberId')];
                $task = ['name' => 'extract_facebook_events',
                         'parameters' => serialize($params),
                         'state' => 0,
                         'member_id' => $this -> session -> get('memberId'),
                         'hash' => time()];
                
                $newTask -> assign($task);
                $newTask -> save();
            }
        }
    	
        $categories = Category::find();
        $categories = $categories->toArray();
        $this->view->setVar('categories', $categories);

        $form = new SearchForm();
        $this -> view -> form = $form;

        $result = array();
        $countResults = 0;
        $Event = new Event();
        $postData = $this->request->getQuery();

        // retrieve data from POST
        if (empty($postData)) {
            $postData = $this->request->getPost();
        }

        // retrieve data from GET
        if (empty($postData)) {
            $postData = $this->session->get('userSearch');
        }

        // delete url url and page params from income data
        unset($postData['_url']);
        if (isset($postData['page'])) {
            unset($postData['page']);
        }

        // check for existence in income data function
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

        // if no location specify - set from user location
        if ($elemExists('searchLocationLatMin', false) || $elemExists('searchLocationLatMax', false) || $elemExists('searchLocationLngMin', false) || $elemExists('searchLocationLngMax', false)) {
            $location = $this->session->get('location');
            $postData['searchLocationLatMin'] = $location->latitudeMin;
            $postData['searchLocationLatMax'] = $location->latitudeMax;
            $postData['searchLocationLngMin'] = $location->longitudeMin;
            $postData['searchLocationLngMax'] = $location->longitudeMax;
        }

        $pageTitle = 'Search results: ';

        // if income data not empty
        if (!empty($postData)) {
            $this->view->setVar('userSearch', $postData);

            // add search condition by title
            if ($elemExists('searchTitle')) {
                $Event->addCondition('Frontend\Models\Event.name LIKE "%'.$postData['searchTitle'].'%"');

                $pageTitle .= 'by title - "'.$postData['searchTitle'].'" | ';
            }

            // add search condition by categories
            /*if ($elemExists('searchCategory')) {
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
            }*/

            // add search condition by location
            if ($elemExists('searchLocationLatMin') && $elemExists('searchLocationLatMax') && $elemExists('searchLocationLngMin') && $elemExists('searchLocationLngMax') && (($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'private') || ($elemExists('searchLocationField') && $postData['searchLocationField'] != ''))) {
                $Event->addCondition('Frontend\Models\Event.latitude BETWEEN '.$postData['searchLocationLatMin'].' AND '.$postData['searchLocationLatMax'].' AND Frontend\Models\Event.longitude BETWEEN '.$postData['searchLocationLngMin'].' AND '.$postData['searchLocationLngMax']);

                $lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
                $lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;

                $loc = new Location();
                $newLocation = $loc -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

                $this->session->set('location', $newLocation);

                $this->cookies->get('lastLat')->delete();
                $this->cookies->get('lastLng')->delete();

                $pageTitle .= 'by location - "'.$newLocation->alias.'" | ';
            }

            // add search condition by dates
            if ($elemExists('searchStartDate') && $elemExists('searchEndDate', false)) {
                $Event->addCondition('((Frontend\Models\Event.start_date <= "'.$postData['searchStartDate'].'" AND Frontend\Models\Event.end_date >= "'.$postData['searchStartDate'].'")');
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('Frontend\Models\Event.start_date >= "'.$postData['searchStartDate'].'")', Event::CONDITION_SIMPLE);

                $pageTitle .= 'from - "'.$postData['searchStartDate'].'" | ';
            }

            if ($elemExists('searchStartDate') && $elemExists('searchEndDate')) {
                $Event->addCondition('((Frontend\Models\Event.start_date BETWEEN "'.$postData['searchStartDate'].'" AND "'.$postData['searchEndDate'].'")');
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('(Frontend\Models\Event.end_date BETWEEN "'.$postData['searchStartDate'].'" AND "'.$postData['searchEndDate'].'")', Event::CONDITION_SIMPLE);
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('(Frontend\Models\Event.start_date <= "'.$postData['searchStartDate'].'" AND Frontend\Models\Event.end_date >= "'.$postData['searchEndDate'].'"))', Event::CONDITION_SIMPLE);

                $pageTitle .= 'from - "'.$postData['searchStartDate'].'" | ';
                $pageTitle .= 'to - "'.$postData['searchEndDate'].'" | ';
            }else {
                $Event->addCondition('Frontend\Models\Event.end_date >= "'.date('Y-m-d H:m:i', time()).'"');
            }

            /*if ($elemExists('searchStartDate')) {
                $Event->addCondition('Frontend\Models\Event.start_date >= "'.$postData['searchStartDate'].'"');

                $pageTitle .= 'by start date - "'.$postData['searchStartDate'].'" | ';
            }

            // add search condition by end date if specify
            if ($elemExists('searchEndDate')) {
                $Event->addCondition('Frontend\Models\Event.end_date <= "'.$postData['searchEndDate'].'"');

                $pageTitle .= 'by end date - "'.$postData['searchEndDate'].'" | ';
            }else {
                $Event->addCondition('Frontend\Models\Event.end_date >= "'.date('Y-m-d H:m:i', time()).'"');
            }*/

            // set order by start date
            $Event->addOrder('Frontend\Models\Event.start_date ASC');

            // search type
            if ($elemExists('searchType')) {
                if ($postData['searchType'] == 'in_map') {

                	if ($elemExists('searchTag')) {
						$Event->addCondition('Frontend\Models\EventTag.tag_id IN (33,34,67)');
					}
                	
                    if ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'global') {
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

                        $result = $Event->fetchEvents(Event::FETCH_ARRAY);
                    } elseif ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                        $result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_DESC, [], true, array('start' => 0, 'limit' => '500'),
                        								   false, false, false, true);
                    } else {
                        $result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_ASC, [], false, array('start' => 0, 'limit' => '500'),
                        								   false, false, false, true);
                    }

                    $countResults = count($result);
                    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
                }else {
                    $page = $this->request->getQuery('page');
                    if (empty($page)) {
                        $page = 1;
                    }

                    if ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'global') {
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
                        
                        if ($elemExists('searchTag')) {
							$Event->addCondition('Frontend\Models\EventTag.tag_id IN (34,33,67)');
						}

                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, 
                        								   Event::ORDER_DESC, 
                        		                           ['page' => $page, 'limit' => 10],
                        								   false,
                        								   array('start' => 0, 'limit' => '500'),
                        								   false, false, false, true);

                    } elseif ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10], true, array('start' => 0, 'limit' => '500'),
                        								   false, false, false, true);
                    } else {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10], false, array('start' => 0, 'limit' => '500'),
                        								   false, false, false, true);
                    }


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
        $this->view->setVar('urlParams', http_build_query($postData));

        if ($postData['searchType'] == 'in_map') {
            $this->view->pick('event/mapEvent');
        } else {
            $this->view->pick('event/eventList');
        }
    }

}

