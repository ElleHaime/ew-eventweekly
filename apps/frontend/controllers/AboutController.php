<?php

namespace Frontend\Controllers;


class AboutController extends \Core\Controller
{
	/**
	 * @Route("/ew/about", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 
	 */
	public function indexAction()
	{
	}

	/**
	 * @Route("/ew/contact", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 
	 */
	public function contactAction()
	{
	}

	
	/**
	 * @Route("/ew/privacy", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});
	 */
	public function privacyAction()
	{
	}
}
