<?php

namespace Frontend\Controllers;

use Core\Utils as _U;

/**
 * @RoutePrefix('/')
 */
class IndexController extends \Core\Controller
{
	/**
	 * @Get('')
	 * @Get('home')
	 */
    public function indexAction()
    {
    }
}

