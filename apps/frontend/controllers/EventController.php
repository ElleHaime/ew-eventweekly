<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Thirdparty\Facebook\Extractor,
	Frontend\Models\Location,
	Frontend\Models\MemberNetwork,
	Frontend\Models\Event,
	Objects\EventImage,
	Objects\EventMember;


class EventController extends \Core\Controllers\CrudController
{
	public function mapAction()
	{
		$location = $this -> session -> get('location');

		$text = urlencode($location);
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=$text&sensor=false&language=ru";
		$result = json_decode(file_get_contents($url));

		if ($result -> status == 'OK') {
			$loc = array(
				'lat' => $result -> results[0] -> geometry -> location -> lat,
				'lng' => $result -> results[0] -> geometry -> location -> lng
			);

			$this -> session -> set('user_loc', $loc);
			$this -> view -> setVar('user_loc', $loc);
		} 
		$this -> view -> setVar('view_action', $this -> request -> getQuery('_url'));
	}


	public function searchAction()
	{
		if ($this -> session -> has("user_token")) {
			$accessToken = $this -> session -> get("user_token");
			$loc = $this -> session -> get("user_loc");

			$this -> facebook = new Extractor();
			$events = $this -> facebook -> getEventsSimpleByLocation($accessToken, $loc);
			if ((count($events[0]) > 0) || (count($events[1]) > 0)) {
				$res['status'] = 'OK';
				$res['message'] = $events;
				echo json_encode($res);
				$this -> parseEvent($events);
				die;
			}
		}		
	}


	public function eventsAction()
	{
		if ($this -> session -> has("user_token")) {
			$accessToken = $this -> session -> get("user_token");
			$loc = $this -> session -> get("user_loc");
	
			$this -> facebook = new Extractor();
			$events = $this -> facebook -> getEventsSimpleByLocation($accessToken, $loc);
	
			if (count($events) > 0) {
				$this -> view -> setVar('userEvents', $events[0]);
				$this -> view -> setVar('friendEvents', $events[1]);
			}
		}
	}


	public function showAction($eventId)
	{
		$event = Event::findFirst(array('id = '.$eventId));	
		if ($this -> session -> has("user_token")) {
			$accessToken = $this -> session -> get("user_token");
			$this -> facebook = new Extractor();
			$eventFb = $this -> facebook -> getEventById($event->fb_uid,$accessToken);

			$eventFb = $eventFb[0]['fql_result_set'][0];
			$eventFb['id'] = $event -> id;

			if ($this -> session -> has('member')) {
				$member = $this -> session -> get('member');
				$conditions = "member_id = ".$member -> id." AND event_id = '".$event -> id."'";
				$eventMember = EventMember::findFirst(array(
					$conditions
				));
				if ($eventMember)
				{
					$eventFb['answer']=(int)$eventMember -> member_status;
				}
				else
					$eventFb['answer']=0;
			}

			$this -> view -> setVar('event', $eventFb);
		}	
	}
	

	public function parseEvent($data)
	{
		$location = new Location();
		$membersList = MemberNetwork::find();
		$eventsList = Event::find();

		if ($membersList) {
			$membersScope = array();
			foreach ($membersList as $mn) {
				$membersScope[$mn -> account_uid] = $mn -> member_id; 
			}
		}
		
		if($eventsList) {
			$eventsScope = array();
			foreach ($eventsList as $ev) {
				$eventsScope[$ev -> fb_uid] = $ev -> id;
			}
		}

		foreach($data as $source => $events) {
			if (!empty($events)) {
				foreach($events as $item => $ev) {
					if (!isset($eventsScope[$ev['eid']])) {
						$result = array();
						
						if (!empty($ev['location'])) {
							$location = new Location();
							$eventLoc = addslashes($ev['location']);
							$eventLocation = $location -> createOnChange($eventLoc);
						} else {
							$eventLocation = '';
						}
						
						$result = array(
							'fb_uid' => $ev['eid'],
							'fb_creator_uid' => $ev['creator'],
							'name' => $ev['name'],
							'description' => $ev['anon'],
							'location_id' => $eventLocation
						); 
						
						if (isset($membersScope[$ev['creator']])) {
							$result['member_id'] = $membersScope[$ev['creator']];
						}
						
						if (!empty($ev['venue'])) {
							if (!empty($ev['venue']['street'])) {
								$result['address'] = $ev['venue']['street'];
							}
							if (!empty($ev['venue']['latitude']) && !empty($ev['venue']['longitude'])) {
						//		$result['coordinates'] = $ev['venue']['latitude'] . ',' . $ev['venue']['longitude'];
							}
						}
						
						$eventObj = new Event(); 
						$eventObj -> assign($result);
						if ($eventObj -> save()) {
							$images = new EventImage();
							$images -> assign(array(
								'event_id' => $eventObj -> id,
								'image' => $ev['pic_square']
							));
							$images -> save();
							$data[$source][$item]['id'] = $eventObj -> id;
						}
					}
					else
						$data[$source][$item]['id'] = $eventsScope[$ev['eid']];
				}
			}			
		}
		return $data;
	}

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
			$conditions = "member_id = ".$member -> id." AND event_id = `".$event_id."`";
			$eventMember = EventMember::findFirst(array(
				$conditions
			));

			echo "<pre>";
			_U::dump($eventMember);
			echo "</pre>";
			die;

			if ($eventMember){
				if ($eventMember -> member_status != $status){
					$eventMember -> assign(array('member_status'=> $status));
					$eventMember -> save();
				}
			}else{
				$eventMember = new EventMember();
				$eventMember -> member_id =  $member -> id;
				$eventMember -> event_id  =  $event_id;
				$eventMember -> member_status = $status;
				$eventMember -> save();
			}

			$ret['STATUS']='OK';
			echo json_encode($ret);
		}
	}

	public function dropLocationAction()
	{
	}
}		
