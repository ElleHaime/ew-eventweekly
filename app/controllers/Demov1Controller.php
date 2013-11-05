<?php

class Demov1Controller extends Phalcon\Mvc\Controller
{


  public function indexAction()
  {
    require_once '../app/libraries/SxGeo/SxGeo.php';

    $this->SxGeo = new SxGeo('../app/libraries/SxGeo/SxGeoCity.dat');

    if ($_SERVER['HTTP_HOST']=='phalcon.loc')
      $ip = '31.172.138.197';
    else
      $ip = $_SERVER['REMOTE_ADDR'];

    $city = $this->SxGeo->getCityFull($ip);
    $loc = array(
      'lat' => $city['lat'],
      'lon' => $city['lon']
    );

    $this->session->set("user_loc", $loc);
    $this->view->setVar('user_loc', $loc);
  }

  public function loginAction()
  {
    $access_token = $this->session->get("user_token");

    if (!empty($access_token))
    {
      $this->view->setVar('is_authorized', true);
    }
  }

  public function profileAction()
  {
    if ($this->session->has("user_loc"))
    {
      $loc = $this->session->get("user_loc");

      $url ='http://maps.googleapis.com/maps/api/geocode/json?latlng='.$loc['lat'].','.$loc['lon'].'&sensor=false&language=en';
      $result=json_decode(file_get_contents($url));

      if ($result->status == 'OK')
      {
        foreach ($result->results as $item)
        {
          foreach ($item->address_components as $level)
          {
            if ($level->types[0]=='country')
              $location['country'] = $level->long_name;
            if ($level->types[0]=='administrative_area_level_1')
              $location['region'] = $level->long_name;
            if ($level->types[0]=='locality')
              $location['city'] = $level->long_name;
            if ($level->types[0]=='sublocality')
              $location['sublocality'] = $level->long_name;
          }
        }
        $loc = $location['country'].', ';
        $loc .= $location['region'].', ';
        $loc .= $location['city'];
        $this->view->setVar('user_loc', $loc);
      }
      else
      {
        echo 'Google thrown an exception: '.$result->status;
      }
    }

  }

  public function saveLocationAction()
  {
    $location = $this->request->getPost('loc');

    $text=urlencode($location);
    $url="http://maps.googleapis.com/maps/api/geocode/json?address=$text&sensor=false&language=ru";
    $result=json_decode(file_get_contents($url));

    if ($result->status == 'OK')
    {
      $loc = array(
        'lat' => $result->results[0]->geometry->location->lat,
        'lon' => $result->results[0]->geometry->location->lng
      );
      $this->session->set("user_loc", $loc);
      $res['status'] = 'OK';
      echo json_encode($res);
    }
    else
    {
      $res['status'] = 'ERROR';
      $res['message'] = $result->status;
      echo json_encode($res);
    }

  }

  public function geoAction()
  {
    if ($this->session->has("user_loc"))
    {
      $loc = $this->session->get("user_loc");

      $url ='http://maps.googleapis.com/maps/api/geocode/json?latlng='.$loc['lat'].','.$loc['lon'].'&sensor=false&language=ru';
      $result=json_decode(file_get_contents($url));

      if ($result->status == 'OK')
      {
        foreach ($result->results as $item)
        {
          foreach ($item->address_components as $level)
          {
            if ($level->types[0]=='country')
              $location['country'] = $level->long_name;
            if ($level->types[0]=='administrative_area_level_1')
              $location['region'] = $level->long_name;
            if ($level->types[0]=='locality')
              $location['city'] = $level->long_name;
            if ($level->types[0]=='sublocality')
              $location['sublocality'] = $level->long_name;
          }
        }
      }
      else
      {
        echo 'Google thrown an exception: '.$result->status;
      }
    }
  }

  public function eventAction()
  {
    $request = new Phalcon\Http\Request();
    $eventId = $request->getPost("eventId");

    if (empty($eventId))
      $eventId = $this->session->get("event_id");
    else
      $this->session->set("event_id", $eventId);

    $accessToken = $this->session->get("user_token");
    require_once '../app/libraries/facebook/fb_extractor.php';
    $this->facebook = new Fb_extractor();
    $event = $this->facebook->getEventById($eventId,$accessToken);

    /*
    if ($event['STATUS']==FALSE)
    {
      $this->session->destroy();
    }
    */

    if (!empty($event[0]['fql_result_set'][0]))
      $event = $event[0]['fql_result_set'][0];

    $this->view->setVar('event', $event);
  }

  public function eventsAction()
  {
    if ($this->session->has("user_token"))
    {
      $accessToken = $this->session->get("user_token");

      require_once '../app/libraries/facebook/fb_extractor.php';
      $this->facebook = new Fb_extractor();

      $loc = $this->session->get("user_loc");
      $events = $this->facebook->getEventsSimple($accessToken,$loc);

      if ( (count($events[0])>0) || (count($events[1])>0) )
      {
        $this->view->setVar('events', $events);
      }
    }
  }

  public function eventsForMapAction()
  {
    if ($this->session->has("user_loc"))
    {
      $loc = $this->session->get("user_loc");
      $this->view->setVar('user_loc', $loc);
    }

    if ($this->session->has("user_token"))
    {
      $accessToken = $this->session->get("user_token");

      require_once '../app/libraries/facebook/fb_extractor.php';
      $this->facebook = new Fb_extractor();

      $events = $this->facebook->getEventsSimpleByLocation($accessToken,$loc);

      if ( (count($events[0])>0) || (count($events[1])>0) )
      {
        $res['status'] = 'OK';
        $res['message'] = $events;
        echo json_encode($res);
      }
    }
  }

  public function mapAction()
  {
    if ($this->session->has("user_loc"))
    {
      $loc = $this->session->get("user_loc");
      $this->view->setVar('user_loc', $loc);
    }
    /*
    if ($this->session->has("user_token"))
    {
      $accessToken = $this->session->get("user_token");

      require_once '../app/libraries/facebook/fb_extractor.php';
      $this->facebook = new Fb_extractor();

      $events = $this->facebook->getEventsSimple($accessToken);

      if ( (count($events[0])>0) || (count($events[1])>0) )
      {
        $this->view->setVar('events', $events);
      }
    }
    */

    /*
    eval("\$result = 50/3*6+124-98;");
    echo $result;
    die;
    */
    /*
    eval("\$val =  50/3*6+124-98;");
    echo "<pre>";
    var_dump($val);
    echo "</pre>";
    die;
    */
  }

  public function quitAction()
  {
    $this->session->destroy();
  }

  public function fbAction()
  {
    require_once '../app/libraries/facebook/fb_extractor.php';
    $this->facebook = new Fb_extractor();
    $user = $this->facebook->getUser();
  }

  public function getTokenAction()
  {
    //$data['uid'] = $this->request->getPost('uid');
    $this->session->set("user_token", $this->request->getPost('access_token'));
    $res['status'] = 'OK';
    $res['message'] = '';
    echo json_encode($res);
  }

}
