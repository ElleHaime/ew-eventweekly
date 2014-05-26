<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Frontend\Models\Location,
	Frontend\Models\Campaign as Campaign,
	Frontend\Models\CampaignCategory,
    Frontend\Models\Event;

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
	public function editAction($id = false)
	{
		parent::editAction();
	}


	/**
	 * @Route("/campaign/delete", methods={"GET", "POST"})
	 * @Acl(roles={'member'});  	 
	 */
	public function deleteAction()
	{
        $data =  $this -> request -> getPost();
        $result['status'] = 'ERROR';

        if (isset($data['id']) && !empty($data['id'])) {
            $campaign = Campaign::findFirst((int)$data['id']);
            if ($campaign) {
                $campaign -> delete();
                $result['status'] = 'OK';
                $result['id'] = (int)$data['id'];
            }
        }
        echo json_encode($result);
	}

    /**
     * @Route("/campaign/event-list/{id:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function eventListAction($id)
    {
        $campaign = Campaign::findFirst((int)$id);

        if ($campaign) {
            $eventModel = new Event();
            $eventModel->addCondition('\Frontend\Models\Event.campaign_id = ' . $id);

            $events = $eventModel->fetchEvents();

            $this->eventListCreatorFlag = true;

            $this->view->pick('event/eventList');

            return [
                'list' => $events,
                'eventListCreatorFlag' => $this->eventListCreatorFlag,
                'listTitle' => $campaign->name . ' events',
                'noListResult' => 'No events in this campaign'
            ];
        }

        $this->response->redirect("/campaign/list");
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
		if (!empty($campaign['location_id'])) {
			$newCamp['location_id'] = $campaign['location_id'];
		} elseif (!empty($campaign['location_latitude']) && !empty($campaign['location_longitude'])) {
			// check location by coordinates
			$location = $loc -> createOnChange(array('latitude' => $campaign['location_latitude'], 
													 'longitude' => $campaign['location_longitude']));
			$newCamp['location_id'] = $location -> id;
		} else {
            $newCamp['location_id'] = '';
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
