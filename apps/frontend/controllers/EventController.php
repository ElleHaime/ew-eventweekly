<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Thirdparty\Facebook\Extractor,
	Frontend\Models\Location,
	Frontend\Models\Venue as Venue,	
	Frontend\Models\MemberNetwork,
	Frontend\Models\Event as Event,
	Objects\EventImage,
	Objects\EventMember;

/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{
	/**
	 * @Route("/map", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 
	 */
	public function mapAction()
	{
		$this -> view -> setVar('view_action', $this -> request -> getQuery('_url'));
		$this -> view -> setVar('link_to_list', true);
	}	
    

	/**
	 * @Route("/eventmap/{lat}/{lan}", methods={"GET", "POST"})
	 * @Route("/eventmap/{lat}/{lan}/{city}", methods={"GET", "POST"})* 
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventmapAction($lat = null, $lng = null, $city = null)
	{
		$events = $this -> searchAction($lat, $lng, $city);

		if (count($events) > 0) {
			$res['status'] = 'OK';
			$res['message'] = $events;
			echo json_encode($res);				
			die();
		} else {
			$res['status'] = 'ERROR';
			$res['message'] = 'no events';
			echo json_encode($res);
			die();
		}
	}


	/**
	 * @Route("/list", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventlistAction()
	{
		$events = $this -> searchAction();

		if (isset($events[0]) || isset($events[1])) {
			$this -> view -> setVar('userEvents', $events[0]);
			$this -> view -> setVar('friendEvents', $events[1]);
			$this -> view -> setVar('eventsTotal', count($events[0]) + count($events[1]));
			$this -> session -> set('eventsTotal', count($events[0]) + count($events[1]));
		} 
		$this -> view -> pick('event/events');
	}



	/**
	 * @Route("/search", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function searchAction($lat = null, $lng = null, $city = null)
	{
        $loc = $this -> session -> get('location');
        
        if(!empty($lat) && !empty($lng)) {
            $loc -> latitude = $lat;
            $loc -> longitude = $lng ;
        }
		if(!empty($city)) {
			$loc -> city = $city;
			$loc -> alias = $city;
		}
		
		$this -> session -> set('location', $loc);
		$eventModel = new Event();

		if ($this -> session -> has('user_token') && $this -> session -> get('user_token') != null) {

			// user registered via facebook and has facebook account
			$events = $eventModel -> grabEventsByFbToken($this -> session -> get('user_token'), $this -> session -> get('location'));
			if (!empty($events['STATUS']) && ($events['STATUS'] == FALSE)) {
				echo $events['MESSAGE'];
				die;
			}

			if ((count($events[0]) > 0) || (count($events[1]) > 0)) {
				$totalEvents = count($events[0]) + count($events[1]);
				$this -> view -> setVar('eventsTotal', $totalEvents);
				$this -> session -> set('eventsTotal', $totalEvents);
				$events = $eventModel -> parseEvent($events);
				
				return $events;

			} else {
				$res['status'] = 'ERROR';
				$res['message'] = 'no events';
				echo json_encode($res);
				die();
			} 

		} else {

			// user registered via email
			$events = array();
			$scale = $this -> geo -> buildCoordinateScale($loc -> latitude , $loc -> longitude);
			$eventsList = $eventModel -> grabEventsByCoordinatesScale($scale);

			if ($eventsList -> count() > 0) {
				$events[0] = array();
				$events[1] = array();

				foreach ($eventsList as $ev) {
					if ($ev -> event -> member_id == $this -> session -> get('memberId')) {
						$elem = 0;
					} else {
						$elem = 1;
					}

					$events[$elem][] = array(
						'id' => $ev -> event -> id,
						'eid' => $ev -> event -> fb_uid,
						'pic_square' => '',
						'address' => $ev -> event -> address,
						'name' => $ev -> event -> name,
						'venue' => array('latitude' => $ev -> latitude,
										 'longitude' => $ev -> longitude),
						'location_id' => $ev -> event -> location_id,
						'anon' => $ev -> event -> description,
						'start_time' => date('F, l d, H:i', strtotime($ev -> event -> start_date)),
						'end_time' => date('F, l d, H:i', strtotime($ev -> event -> end_date)),
					);
				}
			}
			return $events;
		} 
	}

	
	/**
	 * @Route("/event/show/{eventId:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function showAction($eventId)
	{
		$eventModel = new Event();

		$eventObj = $eventModel -> grabEventsByEwId($eventId);
		$event = array(
			'id' => $eventObj -> id,
			'eid' => $eventObj -> fb_uid,
			'name' => $eventObj -> name,
			'description' => $eventObj -> description,
			'start_time' => date('F, l d, H:i', strtotime($eventObj -> start_date)),
			'end_time' => date('F, l d, H:i', strtotime($eventObj -> end_date)),
			'logo' => $eventObj -> logo
		);
		
		if ($event['eid'] != '' && $eventObj -> is_description_full != 1) { 
			$descFull = $eventModel -> grabEventsDescription($event['eid']);
			
			if ($descFull && $descFull != '') {
				$event['description'] = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $descFull);
				$eventObj -> assign(array('description' => $event['description']));
				$eventObj -> save();				
			}
		}
		
		$event['answer'] = 0;
		if ($this -> session -> has('memberId')) {
			$conditions = 'member_id = ' . $this -> session -> get('memberId') . ' AND event_id = ' . $eventId;
			$eventMember = EventMember::findFirst($conditions);
			
			if ($eventMember) {
				$event['answer'] = (int)$eventMember -> member_status;
			} 
		}
		 
		$this -> view -> setVar('event', $event);
	}


	/**
	 * @Route("/event/answer", methods={"GET", "POST"})
	 * @Acl(roles={'member'}); 
	 */
	public function answerAction()
	{
		if ($this -> session -> has('member')) {
			$member = $this -> session -> get('member');

			switch ($this -> request -> getPost('answer', 'string'))
			{
				case 'JOIN': $status = EventMember::JOIN; break;
				case 'MAYBE': $status = EventMember::MAYBE; break;
				case 'DECLINE': $status = EventMember::DECLINE; break;
			}
			$event_id = $this -> request -> getPost('event_id', 'string');

			$eventMember = new EventMember();
			$eventMember -> member_id =  $member -> id;
			$eventMember -> event_id  =  $event_id;
			$eventMember -> member_status = $status;
			$eventMember -> save();

			$ret['STATUS']='OK';
			echo json_encode($ret);
			die;
		}
	}


	/**
	 * @Route("/event/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 	 	 
	 */
	public function listAction()
	{
		parent::listAction();
	}


	/**
	 * @Route("/event/add", methods={"GET", "POST"})
	 * @Route("/event/edit/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction()
	{
		parent::editAction();
	}


	/**
	 * @Route("/event/delete/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}
}		
