<?php

namespace Frontend\Controllers;

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
	public function editAction()
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
}