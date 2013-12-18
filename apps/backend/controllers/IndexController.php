<?php

namespace Backend\Controllers;

class IndexController extends \Phalcon\Mvc\Controller
{
	/**
	 * @Route('/admin/index')
	 * @Acl(roles={'guest','member'});
	 */
    public function indexAction()
    {
    	echo 123;
    	die();
    }

}

