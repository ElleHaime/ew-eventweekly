<?php

namespace Frontend\Controllers;

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
	 * @Route("/campaign/edit/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});  	 
	 */
	public function editAction()
	{
		parent::editAction();
	}


	/**
	 * @Route("/campaign/delete", methods={"GET", "POST"})
	 * @Acl(roles={'member'});  	 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}
}
