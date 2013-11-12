<?php

namespace Core;

use Phalcon\DiInterface,
	Phalcon\Mvc\User\Component,
	Frontend\Models\Members,
	Core\Mail\Mailtemplates,
	Phalcon\Mvc\Dispatcher;

class Mail extends Component
{
	protected $_transport;


	public function send() 
	{

	}

	public function getTemplate($template, $arguments = array())
	{
		
	}


}