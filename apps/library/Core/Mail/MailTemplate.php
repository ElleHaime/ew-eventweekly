<?php

namespace Core\Mail;

class MailTemplates extends \Phalcon\Mvc\Model
{
	const RESTORE_PASSWORD = '';
	const REGISTER_VERIFICATION = '';

	public $id;
	public $name;
	public $description;
	public $subject;
	public $recurring;
	public $mailfrom;
	public $mime;
	public $body;
	public $language; 
	
	
	public function __construct($dependencyInjector)
	{
		$this -> di = $dependencyInjector;
		$this -> annotations = $this -> di -> get('annotations');
	}
	
}