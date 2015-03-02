<?php

namespace Frontend\Controllers;

class AboutController extends \Core\Controller
{
	/**
	 * @Route("/about", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 
	 */
	public function indexAction()
	{
		
	}

	/**
	 * @Route("/contact", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 
	 */
	public function contactAction()
	{
		
	}

}

?>