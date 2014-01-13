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
		  'appId'  => '166657830211705',
		  'secret' => 'e917842e47a57adb93a1e9761af4117a',
	  );
    $this->facebook = new \Thirdparty\Facebook\Facebook($config);
  }

  public function getEventsSimpleByLocation($accessToken, $loc)
  {
    $limit  = 50;
    $latMin = $loc -> latitudeMin;
    $longMin = $loc -> longitudeMin;
    $latMax = $loc -> latitudeMax;
    $longMax = $loc -> longitudeMax;

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
      'SELECT eid, name, description, location, venue, pic_square, creator, start_time, end_time
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid=me())
    		    AND creator=me()
            AND start_time>=now()
            AND venue.longitude <\''.$longMax.'\'  AND venue.longitude >\''.$longMin.'\' AND venue.latitude <\''.$latMax.'\' AND venue.latitude > \''.$latMin.'\''.
            ' LIMIT '.$limit,
      'friends_events_info'=>
      'SELECT eid, name, description, location, venue, pic_square, creator, start_time, end_time
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info))
            AND creator!=me()
            AND start_time>=now()
            AND venue.longitude <\''.$longMax.'\'  AND venue.longitude >\''.$longMin.'\' AND venue.latitude <\''.$latMax.'\' AND venue.latitude > \''.$latMin.'\''.
            ' LIMIT '.$limit
    );
    
    $data = $this -> getFQL($fql, $accessToken);

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

