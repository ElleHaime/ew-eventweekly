<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Objects\Location;

/**
 * @RoutePrefix('/location')
 * @RouteRule(useCrud = true)
 */
class LocationController extends \Core\Controllers\CrudController
{

	/**
	 * @Route("/location/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listAction()
	{
		parent::listAction();
	}


	/**
	 * @Route("/location/add", methods={"GET", "POST"})
	 * @Route("/location/edit/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});
	 */
	public function editAction()
	{
		parent::editAction();
	}


	/**
	 * @Route("/location/delete/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}

	/**
	 * @Route("/location/get/{text}", methods={"GET","POST"})*
	 * @Acl(roles={'member'});
	 */
	public function getAction($text = null)
	{
		$query[] = 'city like "%' . trim($text) . '%"';
		$query[] = 'country like "%' . trim($text) . '%"';
		$query = implode(' or ', $query);
		$locations = Location::find($query);

		$data = array();

		foreach ($locations as $location){
			$data[$location -> id] = $location -> city.', '.$location -> country;
		}

		$res['STATUS'] = 'OK';
		$res['MESSAGE'] = $data;
		echo json_encode($res);
	}
}