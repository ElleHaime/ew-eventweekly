<?php 

namespace Frontend\Models;

use Objects\Event as EventObject,
	Core\Utils as _U,
	Thirdparty\Facebook\Extractor,
	Frontend\Models\Location,
	Frontend\Models\Venue,	
	Frontend\Models\MemberNetwork,
	Objects\EventImage,
	Objects\EventMember;

class Event extends EventObject
{
	public static $eventStatus = array(0 => 'inactive',
							  		   1 => 'active');

	public static $eventRecurring = array('0' => 'one time',
										  '1' => 'every day',
										  '7' => 'every week');



	public function grabEventsByFbId($token, $eventId)
	{
		$eventObj = self::findFirst(array('id = ' . $eventId));
		$fbId = $eventObj -> fb_uid;

		$this -> facebook = new Extractor();
		$event = $this -> facebook -> getEventById($fbId, $token);
		$event = $event[0]['fql_result_set'][0];

		return $event;
	}


	public function grabEventsByEwId($eventId)
	{
		$eventObj = self::findFirst(array('id = ' . $eventId));

		return $eventObj;
	}

	public function grabEventsByFbToken($token, $location)
	{
		$this -> facebook = new Extractor();
		$events = $this -> facebook -> getEventsSimpleByLocation($token, $location);

		return $events;
	}


	public function grabEventsByCoordinatesScale($scale)
	{
		$query = 'select event.*, venue.latitude as latitude, venue.longitude as longitude
					from \Frontend\Models\Event as event 
					left join \Frontend\Models\Venue as venue on event.venue_id = venue.id 
					where 
						venue.latitude between ' . $scale['latMin'] . ' and ' . $scale['latMax'] . '
					and 
						venue.longitude between ' . $scale['lonMin'] . ' and ' . $scale['lonMax'];

		$eventsList = $this -> modelsManager -> executeQuery($query);

		return $eventsList;
	}


	public function parseEvent($data)
	{
//_U::dump($data);		
		$membersList = MemberNetwork::find();
		$eventsList = self::find();
		$locationsList = Location::find();
		$venuesList = Venue::find();
		$cfg = $this -> di -> get('config');

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

						if (isset($ev['pic_square']) && !empty($ev['pic_square'])) {
							$ext = explode('.', $ev['pic_square']);
							$logo = 'fb_' . $ev['eid'] . '.' . end($ext);

							$ch =  curl_init($ev['pic_square']);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
							$content = curl_exec($ch);
							if ($content) {
								$f = fopen($cfg -> application -> uploadDir . 'img/event/' . $logo, 'wb');
								fwrite($f, $content);
								fclose($f);

								$result['logo'] = $logo;
							}
						}

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
						
						$eventObj = new self; 
						$eventObj -> assign($result);
						if ($eventObj -> save()) {
							$images = new EventImage();
							$images -> assign(array(
								'event_id' => $eventObj -> id,
								'image' => $ev['pic_square']
							));
							$images -> save();

							$data[$source][$item]['id'] = $eventObj -> id;
							$data[$source][$item]['logo'] = $eventObj -> logo;
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
						$data[$source][$item]['logo'] = $eventsScope[$ev['eid']]['logo'];						
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
} 