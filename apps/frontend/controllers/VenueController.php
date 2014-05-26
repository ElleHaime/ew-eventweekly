<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Objects\Venue;
/**
 * @RoutePrefix('/venue')
 * @RouteRule(useCrud = true)
 */
class VenueController extends \Core\Controllers\CrudController
{

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

	/**
	 * @Route("/venue/getVenue/{text}", methods={"GET","POST"})*
	 * @Acl(roles={'member'});
	 */
	public function getVenueAction($text = null)
	{
		$query[] = 'name like "%' . trim($text) . '%" group by name';
		$venues = Venue::find($query);

		$data = array();

		foreach ($venues as $venue){
			$data[$venue -> id] = $venue -> name;
		}

		$res['STATUS'] = 'OK';
		$res['MESSAGE'] = $data;
		echo json_encode($res);
	}
}