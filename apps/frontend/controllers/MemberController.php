<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\Member,
    Frontend\Models\Location;


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

        $MemberFilter = new MemberFilter();
        $member_categories = $MemberFilter->getbyId($list->id);

		$this->view->setVars(array(
                'member', $list,
                'categories' => Category::find()->toArray(),
                'member_categories' => $member_categories
            ));

        if ($this->session->has('location_conflict_profile_flag')) {
            $this->view->setVar('conflict', $this->session->get('location_conflict_profile_flag'));
        }
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

    /**
     * @Route("/member/save-filters", methods={"POST"})
     * @Acl(roles={'member'});
     */
    public function saveFiltersAction()
    {
        $Member = $this->session->get('member');
        if (!$Member) {
            return;
        }

        $postData = $this->request->getPost();

        $elemExists = function($elem) use (&$postData) {
            if (!is_array($postData[$elem])) {
                $postData[$elem] = trim(strip_tags($postData[$elem]));
            }
            return (array_key_exists($elem, $postData) && !empty($postData[$elem]));
        };

        $MemberFilter = new MemberFilter();

        if ($elemExists('category')) {

            $toSave = array(
                'member_id' => $Member->id,
                'key' => 'category',
                'value' => $postData['category']
            );

            if ($elemExists('member_filter_category_id')) {
                $toSave['id'] = $postData['member_filter_category_id'];
            }

            $MemberFilter->save($toSave);
        }else {
            $filters = $MemberFilter->findFirst('member_id = '.$Member->id.' AND key = "category"');
            $filters->delete();
        }

        $this->response->redirect('profile');
    }

    /**
     * @Route("/member/update-location", methods={"post"})
     * @Acl(roles={'member'});
     */
    public function updateLocationAction()
    {
        $process = true;

        $member = null;

        $postData = $this->request->getPost();

        $Location = new Location();

        $isLocationExists = $Location::findFirst('city like "%' . trim($postData['city']) . '%" AND country like "%' . trim($postData['country']) . '%"');


        if (!$isLocationExists) {
            $Location->city = $postData['city'];
            $Location->alias = $postData['city'];
            $Location->country = $postData['country'];
            $Location->latitude = $postData['lat'];
            $Location->longitude = $postData['lng'];

            if (!$Location->save()) {
                $process = false;
            }

            $id = $Location->id;
        }else {
            $id = $isLocationExists->id;
            $Location = $isLocationExists;
        }

        if ($process) {
            $sMember = $this->session->get('member');
            $member = Member::findFirst('id = '.$sMember->id);

            if (!$member) {
                $process = false;
            }
        }

        if ($process) {
            $member->location_id = $id;

            if (!$member->save()) {
                $process = false;
            }
        }

        if ($process) {

            $this->session->remove('location_conflict_profile_flag');

            $sMember->location_id = $id;
            $this->session->set('member', $sMember);

            $this->session->set('location', $Location);
            $result = array('status' => true);
        }else {
            $result = array('status' => false);
        }

        exit(json_encode($result));

    }
}