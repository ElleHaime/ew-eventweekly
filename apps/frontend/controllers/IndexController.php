<?php

namespace Frontend\Controllers;

use Core\Utils as _U;

class IndexController extends \Core\Controller
{
    public function indexAction()
    {
	    if ($this -> session -> has('eventsTotal')) {
		    $this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
	    }
    }
}

