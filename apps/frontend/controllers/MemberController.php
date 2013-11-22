<?php

namespace Frontend\Controllers;

use Core\Utils as _U;


class MemberController extends \Core\Controllers\CrudController
{
	/**
	 * @Route("/profile", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function listAction()
	{
		$member = $this -> obj;
		$list = $member::findFirst($this -> session -> get('memberId'));
		if (!$list -> location) {
			$list -> location = $this -> session -> get('location');
		}

		if ($this -> session -> has('eventsTotal')) {
			$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
		}

		$this -> view -> setVar('member', $list);
	}


	/**
	 * @Route("/member/edit", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction()
	{
		parent::editAction();
	}


	public function loadObject()
	{
		$this -> obj = $this -> session -> get('member');
		$this -> setDependencyProperty($this -> obj -> getDependency());
		
		return $this;
	}
	
	
	public function loadRedirect()
	{
		$this -> response -> redirect('profile');
	}

	
	/**
	 * @Route("/profile/refresh", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	   	 
	 */
	public function refreshAction()
	{
		$userData =  $this -> request -> getPost();
				
		if (!empty($userData)) {
			$models = array();
			$res = array();
		
			foreach($userData as $name => $value) {
				$objBar = explode('.', $name);
	
				if (!isset($models[$objBar[0]])) {
					$models[$objBar[0]] = array();
				}
				$models[$objBar[0]][$objBar[1]] = $value;
			}

			if (!empty($models)) {
				foreach ($models as $model => $values) {
					$modelPath = '\Frontend\Models\\' . $model;
					$objModel = new $modelPath();
					if ($model == 'Member') {
						$object = $objModel::findFirst($this -> memberId);
					} else {
						$object = $objModel::findFirst('member_id = ' . $this -> memberId);
					}

					$object -> assign($values);

					if ($object -> save()) {
						if ($model == 'Member') { 
							$this -> session -> set('member', $object);
						}

						foreach($values as $name => $val) {
							$res['updated'][strtolower($model) . '_' . $name] = $val;
						}
					}
				}
			}
			$res['status'] = 'OK';
			echo json_encode($res);
		}	
	}
}