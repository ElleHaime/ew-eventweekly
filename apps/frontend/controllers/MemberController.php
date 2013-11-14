<?php

namespace Frontend\Controllers;

use Core\Utils as _U;


class MemberController extends \Core\Controllers\CrudController
{
	public function listAction()
	{
		$isExternalLogged = $this -> view -> member -> network;
		if ($isExternalLogged) {
			$this -> view -> setVar('acc_external', $isExternalLogged);
			$this -> view -> setVar('acc_uid_network', 'facebook');
		}
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

	public function refreshAction()
	{
		$userData =  $this -> request -> getPost();
		$userData = array('Member.logo' =>  "https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash1/273593_100005266382366_453421009_s.jpg");
		
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