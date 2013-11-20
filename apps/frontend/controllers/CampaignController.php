<?php

namespace Frontend\Controllers;

class CampaignController extends \Core\Controllers\CrudController
{

	public function listAction()
	{
		if ($this -> session -> has('eventsTotal')) {
			$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
		}
		parent::listAction();
	}
}
