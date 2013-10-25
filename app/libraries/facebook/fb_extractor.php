<?php

class Fb_extractor
{

  private $facebook;

  public function __construct()
  {
    require_once '../app/libraries/facebook/facebook.php';
    //require_once 'facebook.php';
    $this->facebook = new Facebook(array(
      'appId'  => '303226713112475',
      'secret' => 'c6ff9b047e23e51c6182bfce06580f23',
    ));
    /*
    echo "<pre>";
    var_dump($this->facebook);
    echo "</pre>";
    die('__construct');
    */
  }

  public function getFriends()
  {

  }

  public function getEventsSimpleByLocation($accessToken,$loc)
  {
    $limit  = 50;
    $offset = 1.8;

    $lat = floatval($loc['lat']);
    $long = floatval($loc['lon']);

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
      'SELECT eid, name, creator, substr(description,0,120), location, venue, pic_square
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid=me())
            AND start_time>=now()
            AND venue.longitude <\''.$lngpo.'\'  AND venue.longitude >\''.$lngmo.'\' AND venue.latitude <\''.$latpo.'\' AND venue.latitude > \''.$latmo.'\''.
            ' LIMIT '.$limit,
      'friends_events_info'=>
      'SELECT eid, name, substr(description,0,120), location, venue, pic_square
          FROM event
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info))
            AND start_time>=now()
            AND venue.longitude <\''.$lngpo.'\'  AND venue.longitude >\''.$lngmo.'\' AND venue.latitude <\''.$latpo.'\' AND venue.latitude > \''.$latmo.'\''.
            ' LIMIT '.$limit
    );

    /*
    echo "<pre>";
    var_dump($fql);
    echo "</pre>";
    die;
    */

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      echo $data['MESSAGE'];
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
            WHERE eid IN (SELECT eid FROM event_member WHERE uid IN (SELECT uid FROM #friends_uid_info))
            AND start_time>=now() LIMIT '.$limit
    );

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      echo $data['MESSAGE'];
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
            AND start_time>=now()'
    );

    $data = $this->getFQL($fql,$accessToken);
    if ($data['STATUS']==FALSE)
    {
      echo $data['MESSAGE'];
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

  public function getFQL($query,$access_token)
  {
    try
    {
      $ret_obj = $this->facebook->api(array(
        'method' => 'fql.multiquery',
        'queries' => $query,
        'access_token' => $access_token,
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
        $ret['MESSAGE'] = 'Facebook throws an exception. Error code:'.$e->getCode().' Error message:'.$e->getMessage();
        return $ret;
      }
    }
  }

}

