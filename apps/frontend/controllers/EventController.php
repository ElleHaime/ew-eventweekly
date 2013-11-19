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


class EventController extends \Core\Controllers\CrudController
{
	public function mapAction()
	{
		$this -> view -> setVar('view_action', $this -> request -> getQuery('_url'));
		$this -> view -> setVar('link_to_list', true);
		if ($this -> session -> has('eventsTotal')) {
			$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
		}
	}


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

	public function eventlistAction()
	{
		$events = $this -> searchAction();

		if (count($events) > 0) {
			$this -> view -> setVar('userEvents', $events[0]);
			$this -> view -> setVar('friendEvents', $events[1]);
			$this -> view -> setVar('eventsTotal', count($events[0]) + count($events[1]));
		} 
		$this -> view -> pick('event/events');
	}


	public function searchAction($lat = null, $lng = null, $city = null)
	{
        $loc = $this -> session -> get('location');
        if(!empty($lat) && !empty($lng)) {
            $loc->latitude = $lat;
            $loc->longitude = $lng ;

            if(!empty($city)) {
                $loc->city = $city;
                $loc->alias = $city;
            }

            $this->session->set('location', $loc);
        }

		if ($this -> session -> has('user_token') && $this -> session -> get('user_token') != null) {

			// user registered via facebook and has facebook account
			$this -> facebook = new Extractor();
			$events = $this -> facebook -> getEventsSimpleByLocation($this -> session -> get('user_token'), $loc);
			if ((count($events[0]) > 0) || (count($events[1]) > 0)) {
				$totalEvents=count($events[0])+count($events[1]);
				$this -> view -> setVar('eventsTotal', $totalEvents);
				$this->session->set("eventsTotal", $totalEvents);
				$events = $this -> parseEvent($events);
				return $events;
			} else {
				$res['status'] = 'ERROR';
				$res['message'] = 'no events';
				echo json_encode($res);
				die();
			}

		} else {

			// user registered via email
			$location = $loc;
			$modelPath = $this -> getModelPath();
			$scale = $this -> geo -> buildCoordinateScale($location -> latitude , $location -> longitude);
			$query = 'select event.*, venue.latitude as latitude, venue.longitude as longitude
						from ' . $modelPath . 'Event as event 
						left join ' . $modelPath . 'Venue as venue on event.venue_id = venue.id 
						where 
							venue.latitude between ' . $scale['latMin'] . ' and ' . $scale['latMax'] . '
						and 
							venue.longitude between ' . $scale['lonMin'] . ' and ' . $scale['lonMax'];
			$eventsList = $this -> modelsManager -> executeQuery($query);
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


	public function showAction($eventId)
	{
		$eventObj = Event::findFirst(array('id = ' . $eventId));
	
		if ($this -> session -> has('user_token')) {
			$accessToken = $this -> session -> get('user_token');
			$this -> facebook = new Extractor();
			$event = $this -> facebook -> getEventById($eventObj -> fb_uid, $accessToken);

			if ($this -> session -> has('eventsTotal')) {
				$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
			}

			$event = $event[0]['fql_result_set'][0];
			$event['id'] = $eventObj -> id;
		} else {
			$event = array(
				'id' => $eventObj -> id,
				'name' => $eventObj -> name,
				'description' => $eventObj -> description,
				'start_time' => date('F, l d, H:i', strtotime($eventObj -> start_date)),
				'end_time' => date('F, l d, H:i', strtotime($eventObj -> end_date)),
				'pic_square' => ''
			);
		}

		$event['answer'] = 0;
		if ($this -> session -> has('memberId')) {
			$conditions = 'member_id = ' . $this -> session -> get('memberId') . ' AND event_id = ' . $eventObj -> id;
			$eventMember = EventMember::findFirst($conditions);
			
			if ($eventMember) {
				$event['answer'] = (int)$eventMember -> member_status;
			} 
		} 
		$this -> view -> setVar('event', $event);
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


	public function parseEvent($data)
	{
		$membersList = MemberNetwork::find();
		$eventsList = Event::find();
		$locationsList = Location::find();
		$venuesList = Venue::find();

		if ($membersList) {
			$membersScope = array();
			foreach ($membersList as $mn) {
				$membersScope[$mn -> account_uid] = $mn -> member_id; 
			}
		}
		
		if ($eventsList) {
			$eventsScope = array();
			foreach ($eventsList as $ev) {
				$eventsScope[$ev -> fb_uid] = array('id' => $ev -> id,
													'start_date' => $ev -> start_date,
													'end_date' => $ev -> end_date);
			}
		}

		if ($venuesList) {
			$venuesScope = array();
			foreach ($venuesList as $vn) {
				$venuesScope[$vn -> fb_uid] = array('venue_id' => $vn -> id,
													'address' => $vn -> address,
													'location_id' => $vn -> location_id);
			}
		}

		if ($locationsList) {
			$locationsScope = array();
			foreach ($locationsList as $loc) {
				$locationsScope[$loc -> id] = array('lat' => $loc -> latitude,
													'lon' => $loc -> longitude,
													'city' => $loc -> city,
													'country' => $loc -> country);
			}
		}

		foreach($data as $source => $events) {
			if (!empty($events)) {
				foreach($events as $item => $ev) {

					if (!isset($eventsScope[$ev['eid']])) {
						$result = array();
						$result['fb_uid'] = $ev['eid'];
						$result['fb_creator_uid'] = $ev['creator'];
						$result['description'] = $ev['anon'];
						$result['name'] = $ev['name'];

						if (!empty($ev['start_time'])) {
							$result['start_date'] = date('Y-m-d H:i:s', strtotime($ev['start_time']));
						}
						if (!empty($ev['end_time'])) {
							$result['end_date'] = date('Y-m-d H:i:s', strtotime($ev['end_time']));
						}

						if (isset($membersScope[$ev['creator']])) {
							$result['member_id'] = $membersScope[$ev['creator']];
						}

						$eventLocation = '';

						if (!empty($ev['venue'])) {
							if (!isset($venuesScope[$ev['venue']['id']])) {

								// check location by city and country of venue
								foreach ($locationsScope as $loc_id => $coords) {
									if ($ev['venue']['city'] == $coords['city'] && $ev['venue']['country'] == $coords['country']) {
										$eventLocation = $loc_id;
										break;
									}
								}

								// check location by venue coordinates
								if ($eventLocation == '') {
									$scale = $this -> geo -> buildCoordinateScale($ev['venue']['latitude'], $ev['venue']['longitude']);	
									foreach ($locationsScope as $loc_id => $coords) {
										if ($scale['latMin'] <= $coords['lat'] && $coords['lat'] <= $scale['latMax'] &&
											$scale['lonMin'] <= $coords['lon'] && $coords['lon'] <= $scale['lonMax'])
										{
											$eventLocation = $loc_id;
											break;
										}
									}
								} 

								// create new location from coordinates
								if ($eventLocation == '') {
									$locationArgs = $this -> geo -> getLocation(array('latitude' => $ev['venue']['latitude'], 
																			 		  'longitude' => $ev['venue']['longitude']));
									$loc = $this -> locator -> createOnChange($locationArgs);
									$eventLocation = $loc -> id;

									$locationsScope[$loc -> id] = array(
														'lat' => $loc -> latitude,
														'lon' => $loc -> longitude,
														'city' => $loc -> city,
														'country' => $loc -> country);
								}

								$venueObj = new Venue();
								$venueObj -> assign(array(
									'fb_uid' => $ev['venue']['id'],
									'location_id' => $eventLocation,
									'name' => $ev['location'],
									'address' => $ev['venue']['street'],
									'latitude' => $ev['venue']['latitude'],
									'longitude' => $ev['venue']['longitude']
								));
								if ($venueObj -> save()) {
									$result['venue_id'] = $venueObj -> id;
									$result['address'] = $ev['venue']['street'];
									$result['location_id'] = $venueObj -> location_id;

									$venuesScope[$venueObj -> id] = array(
															'address' => $venueObj -> address,
															'location_id' => $venueObj -> location_id);
								}
							} else {
								$result['venue_id'] = $venuesScope[$ev['venue']['id']]['venue_id'];
								$result['address'] = $venuesScope[$ev['venue']['id']]['address'];	
								$result['location_id'] = $venuesScope[$ev['venue']['id']]['location_id'];	
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
							if (!empty($eventObj -> start_date)) {
								$data[$source][$item]['start_date'] = date('F, l d, H:i', strtotime($eventObj -> start_date));
							}
							if (!empty($eventObj -> end_date)) {
								$data[$source][$item]['end_date'] = date('F, l d, H:i', strtotime($eventObj -> end_date));
							}

							$eventsScope[$ev['eid']] = $eventObj -> id;
						}
					} else {
						$data[$source][$item]['id'] = $eventsScope[$ev['eid']]['id'];
						if (!empty($eventsScope[$ev['eid']]['start_date'])) {
							$data[$source][$item]['start_date'] = date('F, l d, H:i', strtotime($eventsScope[$ev['eid']]['start_date']));
						}
						if (!empty($eventsScope[$ev['eid']]['end_date'])) {
							$data[$source][$item]['end_date'] = date('F, l d, H:i', strtotime($eventsScope[$ev['eid']]['end_date']));
						}
					}
				}
			}			
		}
		return $data;
	}

	public function dropLocationAction()
	{
	}
}		
