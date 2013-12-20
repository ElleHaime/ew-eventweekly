<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Frontend\Models\Location,
	Frontend\Models\Campaign as Campaign,
	Frontend\Models\CampaignCategory;

/**
 * @RoutePrefix('/campaign')
 * @RouteRule(useCrud = true)
 */
class CampaignController extends \Core\Controllers\CrudController
{

	/**
	 * @Route("/campaign/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'});  	 
	 */
	public function listAction()
	{
		parent::listAction();

	}


	/**
	 * @Route("/campaign/add", methods={"GET", "POST"})
	 * @Route("/campaign/edit", methods={"GET", "POST"})
	 * @Route("/campaign/edit/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});  	 
	 */
	public function editAction()
	{
		parent::editAction();
	}

	public function setEditExtraRelations()
	{
		$this -> editExtraRelations = array(
			'location' => array('latitude', 'longitude')
		);
	}

	/**
	 * @Route("/campaign/delete", methods={"GET", "POST"})
	 * @Acl(roles={'member'});  	 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}

	public function processForm($form) 
	{
		_U::dump($form -> getFormValues(), true);
		_U::dump($this -> request -> getUploadedFiles(), true);
//die();
		$campaign = $form -> getFormValues();
		$loc = new Location();
		$newCamp = array();

		$newCamp['name'] = $campaign['name'];
		$newCamp['description'] = $campaign['description'];
		$newCamp['member_id'] = $this -> session -> get('memberId');
		$newCamp['logo'] = $campaign['logo'];
		$newCamp['address'] = $campaign['address'];
		
		// process location
		if (!empty($campaign['location_latitude']) && !empty($campaign['location_longitude'])) {
			// check location by coordinates
			$location = $loc -> createOnChange(array('latitude' => $campaign['location_latitude'], 
													 'longitude' => $campaign['location_longitude']), 
													 array('latitude', 'longitude'));
			$newCamp['location_id'] = $location -> id;

		} 
		// location wasn't found
		if (!isset($newCamp['location_id'])) {
			if (!empty($campaign['location'])) {
				$location = $loc -> createOnChange(array('city' => $campaign['location']), array('city'));
				$newCamp['location_id'] = $location -> id; 
			}
		}

		//process image
		foreach ($this -> request -> getUploadedFiles() as $file) {
			$newCampaign['logo'] = $file -> getName();
			$logo = $file;
		}
//_U::dump($newCampaign);	

		if (!empty($campaign['id'])) {
			$camp = Campaign::findFirst($campaign['id']);
		} else {
			$camp = new Campaign();
		}
		$camp -> assign($newCamp);
		if ($camp -> save()) {
			// save image
			if (isset($logo)) {
				$logo -> moveTo($this -> config -> application -> uploadDir . 'img/campaign/' . $logo -> getName());
			}
		}

        $this -> loadRedirect();
	}
}
