<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Frontend\Models\Venue,
	Frontend\Models\VenueImage,
	Frontend\Models\Event;
/**
 * @RoutePrefix('/venue')
 * @RouteRule(useCrud = true)
 */
class VenueController extends \Core\Controllers\CrudController
{
	public $activeTab		= 'profile';
	public $featuresBlock 	= ['services', 'specialties'];
	public $profileBlockLeft 	= ['worktime', 'payment', 'pricerange', 'parking', 'phone', 'email'];
	public $profileBlockRight 	= ['phone', 'email'];
	
	
	/**
     * @Route("/venue/{slugUri}-{objectId:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
	 */
	public function showAction($slug, $objectId)
	{
		$venue = Venue::findFirst($objectId);

		if ($venue) {
			if ($cover = (new VenueImage()) -> getCover($venue)) {
				$this -> view -> setVar('cover', $cover); 
			}
			if ($events = (new Event()) -> getEventsByVenue($venue)) {
				$this -> activeTab = 'events';
				$this -> view -> setVar('events', $events);
			}
			if ($gallery = (new VenueImage()) -> getGallery($venue)) {
				$this -> activeTab = 'gallery';
				$this -> view -> setVar('gallery', $gallery);
			}
			
			$venue -> worktime = unserialize($venue -> worktime);
			$venue -> payment = $this -> processFeatures($venue -> payment);
			$venue -> parking = $this -> processFeatures($venue -> parking);
			$venue -> services = $this -> processFeatures($venue -> services);
			$venue -> specialties = $this -> processFeatures($venue -> specialties);
// _U::dump($events);			
			$this -> view -> setVar('venue', $venue);
			$this -> view -> setVar('featuresBlock', $this -> featuresBlock);
			$this -> view -> setVar('profileBlockLeft', $this -> profileBlockLeft);
			$this -> view -> setVar('profileBlockRight', $this -> profileBlockRight);
			$this -> view -> setVar('activeTab', $this -> activeTab);
			$this -> view -> setVar('includeVenueCss', 1);
		} else {
			_U::dump('ooooooooooooooooops');		
		}
	}
	

	/**
	 * @Route("/venue/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'}); 
	 */
	public function listAction()
	{
		parent::listAction();
	}


	/**
	 * @Route("/venue/add", methods={"GET", "POST"})
	 * @Route("/venue/edit/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'}); 
	 */
	public function editAction($id = false)
	{
		parent::editAction();
	}

	
	/**
	 * @Route("/venue/delete/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'}); 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}

	/**
	 * @Route("/venue/getAddress/{text}", methods={"GET","POST"})*
	 * @Acl(roles={'member'});
	 */
	public function getAddressAction($text = null)
	{
		$query[] = 'address like "%' . trim($text) . '%" group by address';
		$addresses = Venue::find($query);

		$data = array();

		foreach ($addresses as $address){
			$data[$address -> id] = $address -> address;
		}

		$res['STATUS'] = 'OK';
		$res['MESSAGE'] = $data;
		echo json_encode($res);
	}
	
	
	private function processFeatures($arg = null)
	{
		$result = [];
		
		if (!is_null($arg)) {
			$feature = unserialize($arg);

			foreach($feature as $key => $value) {
				if ($value == 1) {
					$result[] = ucfirst(str_replace('_', ' ', $key));
				}
			}
		}
		
		return $result;
	}
}