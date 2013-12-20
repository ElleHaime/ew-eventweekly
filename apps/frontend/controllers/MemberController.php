<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\Member,
    Frontend\Models\Location,
    Frontend\Form\ChangePassForm,
    Frontend\Form\MemberForm;


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
        $memberForm = new MemberForm($list);

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

        $this->view->memberForm = $memberForm;
	}


	/**
	 * @Route("/member/edit", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction()
	{
        $cfg = $this -> di -> get('config');
        $member = Member::findFirst('id = '.$this->session->get('memberId'));

        $form = new MemberForm($member);

        if ($this->request->isPost()) {
            $formValues = $this->request->getPost();

            if ($form->isValid($formValues)) {
                $member->extra_email = $formValues['extra_email'];
                $member->name = $formValues['name'];
                $member->address = $formValues['address'];
                $member->phone = $formValues['phone'];

                if ($this->request->hasFiles() == true) {
                    $file = array_shift($this->request->getUploadedFiles());

                    $imgExts = array('image/jpeg', 'image/png');

                    if (in_array($file->getType(), $imgExts)) {
                        $parts = pathinfo($file->getName());

                        $filename = $parts['filename'] . '_' . md5($file->getName() . date('YmdHis')) . '.' . $parts['extension'];
                        $file->moveTo($cfg -> application -> uploadDir . 'img/logos/' . $filename);

                        $oldFile = ROOT_APP . 'public' . $member->logo;
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                        $member->logo = '/upload/img/logos/' . $filename;
                    }
                }

                if (!$member->save()) {
                    $this->setFlash('Error while saving your profile data password! Call to your admin!', 'error');
                } else {
                    $this->setFlash('Your data was successfully changed!');

                    $this->session->set('member', $member);
                    $this->loadRedirect();
                }
            } else {
                $form->setFormValues($formValues);
            }
        }

        $this->view->form = $form;
	}


	public function loadObject()
	{
		$this -> obj = $this -> session -> get('member');
		$this -> setDependencyProperty($this -> obj -> getDependency());
		
		return $this;
	}
	
	
	public function loadRedirect()
	{
		$this -> response -> redirect('/profile');
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

        $this->loadRedirect();
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

    /**
     * @Route("/profile/change-password", methods={"get","post"})
     * @Acl(roles={'member'});
     */
    public function changePasswordAction() {
        $form = new ChangePassForm();

        if ($this->request->ispost()) {
            if ($form->isValid($this->request->getPost())) {

                $postData = $this->request->getPost();

                $member = Member::findFirst('id = '.$this->session->get('memberId'));

                if (!$this->security->checkHash($postData['old_password'], $member->pass)) {
                    $this->setFlash('Wrong old password!', 'error');
                }else {
                    $member->pass = $this->security->hash($postData['password']);

                    if (!$member->save()) {
                        $this->setFlash('Error while saving your new password! Call to your admin!', 'error');
                    }else {
                        $this->eventsManager->fire('App.Auth.Member:afterPasswordSet', $this, $member);

                        $this->setFlash('Your password was successfully changed!');

                        $this->loadRedirect();
                    }
                }
            }
        }

        $this->view->form = $form;
    }
}