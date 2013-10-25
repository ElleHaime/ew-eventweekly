<?php

class FacebookController extends Phalcon\Mvc\Controller
{

  public function onConstruct()
  {
    require_once '../app/libraries/facebook/facebook.php';
    $this->facebook = new Facebook(array(
      'appId'  => '303226713112475',
      'secret' => 'c6ff9b047e23e51c6182bfce06580f23',
    ));
  }

  public function indexAction()
  {
    echo "<pre>";
    var_dump(geoip_record_by_name('31.172.138.197'));
    echo "</pre>";
    die;

    //$this->view->setVar("post", 'somedata');

    /*
    echo "<pre>";
    var_dump($this->facebook);
    echo "</pre>";
    */

    //onConstruct()
    //setVar
  }

}

