<?php

namespace Thirdparty\Facebook;

use Thirdparty\Facebook\FacebookApiException;

class Extractor
{

  private $facebook;

  public function __construct()
  {
    //require_once 'facebook.php';

	  $config = array(
		  'appId'  => '423750634398167',
		  'secret' => '3ce97fc65389b3a67bd5cfad27168ada',
	  );
    $this->facebook = new \Thirdparty\Facebook\Facebook($config);
  }

  public function compareResults($data,$conditions)
  {
    foreach ($data as $field)
    {

      /*
      echo "<pre>";
      var_dump($field['']);
      echo "</pre>";
      */
    }
  }

  public function getFriendsCount($accessToken)
  {
    $fql = array(
      'friends_count' =>
      'SELECT friend_count FROM user WHERE uid = me()'
    );
    $data = $this->getFQL($fql,$accessToken);
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
  }

  public function getEventMembers($eventId,$accessToken)
  {

    $fql = array(
    'event_member' =>
    'SELECT rsvp_status, uid
          FROM event_member
              WHERE eid='.$eventId.' AND uid IN (SELECT uid2 FROM friend WHERE uid1 = me()) AND rsvp_status!=\'declined\'',
    'friends_info'=>
    'SELECT uid, first_name, last_name, pic_square
            FROM user
              WHERE uid IN (SELECT uid FROM #event_member)'
    );

    $data = $this->getFQL($fql,$accessToken);

    if ($data['STATUS']==FALSE)
    {
      return $data;
    }

    return $data['MESSAGE'];
  }

  public function getFriends()
  {

  }

  public function getEventsSimpleByLocation($accessToken,$loc)
  {
    $limit  = 50;
    $offset = 1.8;

    $lat = floatval($loc['lat']);
    $long = floatval($loc['lng']);

    $lngpo = str_replace('.',',', $long+$offset);
    $lngmo = str_replace('.',',', $long-$offset);
    $latpo = str_replace('.',',', $lat+$offset);
    $latmo = str_replace('.',',', $lat-$offset);

    $fql = array(
      'my_id'=>
      'SELECT uid
          FROM user
            WHERE uid=me()',
      'friends_uid_info'=>
      'SELECT uid
          FROM user
            WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 IN (SELECT uid FROM #my_id)) LIMIT '.$limit,
      'my_events_info'=>
      'SELECT eid, name, substr(description,0,120), location, venue, pic_square, creator, start_time, end_time
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid=me())
    		AND creator=me()
            AND start_time>=now()
            AND venue.longitude <\''.$lngpo.'\'  AND venue.longitude >\''.$lngmo.'\' AND venue.latitude <\''.$latpo.'\' AND venue.latitude > \''.$latmo.'\''.
            ' LIMIT '.$limit,
      'friends_events_info'=>
      'SELECT eid, name, substr(description,0,120), location, venue, pic_square, creator, start_time, end_time
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info))
            AND creator!=me()
            AND start_time>=now()
            AND venue.longitude <\''.$lngpo.'\'  AND venue.longitude >\''.$lngmo.'\' AND venue.latitude <\''.$latpo.'\' AND venue.latitude > \''.$latmo.'\''.
            ' LIMIT '.$limit
    );

    $data = $this->getFQL($fql,$accessToken);

/*
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die;
*/

    if ($data['STATUS']==FALSE)
    {
      return $data;
    }

    $data=$data['MESSAGE'];

    foreach ($data as $key => $result)
    {
      if ($result['name'] == 'my_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
      if ($result['name'] == 'friends_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
    }

    return $events;
  }

  public function getEventById($eventId,$accessToken)
  {
    $fql = array(
      'event' =>
        'SELECT eid, name, description, location, venue, pic_square, creator, end_time, pic_square, start_time, update_time
          FROM event WHERE eid='.$eventId
    );

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      return $data;
      die;
    }
    return $data['MESSAGE'];
  }

  public function getEventsSimple($accessToken)
  {
    $limit = 50;

    $fql = array(
      'my_id'=>
      'SELECT uid
          FROM user
            WHERE uid=me()',
      'friends_uid_info'=>
      'SELECT uid
          FROM user
            WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 IN (SELECT uid FROM #my_id)) LIMIT '.$limit,
      'my_events_info'=>
      'SELECT eid, name, creator, substr(description,0,120), location, venue, pic_square
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid=me())
            AND start_time>=now() LIMIT '.$limit,
      'friends_events_info'=>
      'SELECT eid, name, substr(description,0,120), location, venue, pic_square
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info) )
            AND creator!=me()
            AND start_time>=now() LIMIT '.$limit
    );

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      return $data;
      die;
    }

    $data=$data['MESSAGE'];

    foreach ($data as $key => $result)
    {
      if ($result['name'] == 'my_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
      if ($result['name'] == 'friends_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
    }

    return $events;
  }

  public function getEventsFull($accessToken)
  {
    $fql = array(
      'my_id'=>
      'SELECT uid
          FROM user
            WHERE uid=me()',
      'friends_uid_info'=>
      'SELECT uid
          FROM user
            WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 IN (SELECT uid FROM #my_id))',
      'my_events_info'=>
      'SELECT eid, name, creator, description, location, venue, start_time, end_time, update_time, pic_big
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid=me())
            AND start_time>=now()',
      'friends_events_info'=>
      'SELECT eid, name, creator, description, location, venue, start_time, end_time, update_time, pic_big
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info))
            AND creator!=me()
            AND start_time>=now()'
    );

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      return $data;
      die;
    }

    $data=$data['MESSAGE'];

    foreach ($data as $key => $result)
    {
      if ($result['name'] == 'my_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
      if ($result['name'] == 'friends_events_info')
      {
        $events[] = $data[$key]['fql_result_set'];
      }
    }

    return $events;
  }

  public function getUser()
  {

    /*
    echo "<pre>";
    var_dump($this->facebook);
    echo "</pre>";
    */
    //return $this->facebook;
    //return $this->facebook->getUser();
  }

  public function getFQL($query,$accessToken)
  {
    try
    {
      $ret_obj = $this->facebook->api(array(
        'method' => 'fql.multiquery',
        'queries' => $query,
        'access_token' => $accessToken,
      ));

      if (!empty($ret_obj))
      {
        $ret['STATUS'] = TRUE;
        $ret['MESSAGE'] = $ret_obj;
        return $ret;
      }
      else
      {
        $ret['STATUS'] = FALSE;
        $ret['MESSAGE'] = 'Nothing found.';
        return $ret;
      }
    }
    catch(FacebookApiException $e)
    {
      if (190 == $e->getCode())
      {
        $ret['STATUS'] = FALSE;
        $ret['MESSAGE'] = 'Access token expired';
        return $ret;
      }
      else
      {
        $ret['STATUS'] = FALSE;
        $ret['MESSAGE'] = 'Facebook throws an exception. Error code:'.$e->getCode().' '.$e->getMessage();
        return $ret;
      }
    }
  }

}

