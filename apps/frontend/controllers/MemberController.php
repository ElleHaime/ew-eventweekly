<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\Member,
    Frontend\Models\Location,
    Frontend\Models\Tag,
    Frontend\Models\Cron,
    Frontend\Form\ChangePassForm,
    Frontend\Form\MemberForm,
    Frontend\Form\LoginForm,
    Frontend\Models\MemberNetwork,
	Frontend\Models\EventLike,
	Frontend\Models\EventMember,
	Frontend\Models\EventMemberFriend;


class MemberController extends \Core\Controllers\CrudController
{
	/**
	 * @Route("/member/profile", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function listAction()
	{
		if ($this -> session -> has('passwordChanged') && $this -> session -> get('passwordChanged') === true) {
			$this -> session -> set('passwordChanged', false);
			$this -> view -> setVar('passwordChanged', true);
		} 
		
		$member = $this -> obj;
		$list = $member::findFirst($this -> session -> get('memberId'));
		if (!$list -> location) {
			$list -> location = $this -> session -> get('location');
		}
		$form = new MemberForm($list);
		$this->view->form = $form;
		
		if ($this -> session -> has('eventsTotal')) {
			$this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
		}

		$this -> view -> setVars(['member' => $list,
								  'userFilters' => $this -> filtersBuilder -> getFormFilters()['userFilters']]);

        if ($this->session->has('location_conflict_profile_flag')) {
            $this->view->setVar('conflict', $this->session->get('location_conflict_profile_flag'));
        }
	}


	/**
	 * @Route("/member/edit", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction($id = false)
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

                if ($this->request->hasFiles() != 0) {
                    $uploadedFile = $this->request->getUploadedFiles();
                    $file = array_shift($uploadedFile);

                    $imgExts = array('image/jpeg', 'image/png');

                    if (in_array($file->getType(), $imgExts)) {
                        $parts = pathinfo($file->getName());

                        $filename = $parts['filename'] . '_' . md5($file->getName() . date('YmdHis')) . '.' . $parts['extension'];
                        $filepath = $cfg -> application -> uploadDir . 'img/logos';
                        if (!is_dir($filepath)) {
                            mkdir($filepath, 0777, true);
                        }
                        $file->moveTo($filepath . '/' . $filename);

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
	
	
	public function loadRedirect($params = [])
	{
		$this -> response -> redirect('/member/profile');
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
        $postData = $this->request->getPost();
//_U::dump($postData);
        $elemExists = function($elem) use (&$postData) {
            if (!is_array($postData[$elem])) {
                $postData[$elem] = trim(strip_tags($postData[$elem]));
            }
            return (array_key_exists($elem, $postData) && !empty($postData[$elem]));
        };

        $memberFilters = MemberFilter::find(['member_id = ' . $this -> session -> get('memberId')]);
        if ($memberFilters -> count() != 0) {
			foreach ($memberFilters as $mf) {
				$mf -> delete();
			}
		}
   
		if (!empty($postData['category']) && $elemExists('category')) {
			$memberFilters = new MemberFilter();
			$memberFilters -> assign(['member_id' => $this -> session -> get('memberId'),
			           					'key' => 'category',
			           					'value' => json_encode(array_keys($postData['category']))]);
			$memberFilters -> save();
		}
            
		if (!empty($postData['tag']) && $elemExists('tag')) {
			$memberFilters = new MemberFilter();
			$memberFilters -> assign(['member_id' => $this -> session -> get('memberId'),
           								'key' => 'tag',
           								'value' => json_encode(array_keys($postData['tag']))]);
   			$memberFilters -> save();
        }
        
        $this -> filtersBuilder -> resetPreset();

        $this -> loadRedirect();
    }

    /**
     * @Route("/member/update-location", methods={"post"})
     * @Acl(roles={'member'});
     */
    public function updateLocationAction()
    {
        $process = true;

        $member = null;

        $postData = $this -> request-> getPost();

        $newLoc = new Location();
        $Location = $newLoc -> createOnChange(['place_id' => $postData[\Core\Geo::GMAPS_PLACE],
        										'city' =>  $postData[\Core\Geo::GMAPS_CITY],
        										'state' =>  $postData[\Core\Geo::GMAPS_STATE],
        										'country' =>  $postData[\Core\Geo::GMAPS_COUNTRY],
        										'latitude' => $postData['lat'], 
        										'longitude' => $postData['lng']]);
        $id = $Location->id;

        if ($process) {
            $sMember = $this->session->get('member');
            if (!$member = Member::findFirst('id = ' . $sMember -> id)) $process = false;
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

            /*$this->session->set('location', $Location);

            $this->cookies->get('lastLat')->delete();
            $this->cookies->get('lastLng')->delete();
            $this->cookies->get('lastCity')->delete(); */

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
						$this -> session -> set('passwordChanged', true);
                        $this->loadRedirect();
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * @Route("/member/get-private-preset", methods={'get'})
     * @Acl(roles={'guest', 'member'});
     */
    public function getPrivatePresetAction()
    {
        $response = [
            'errors' => false
        ];

        if ($this->session->has('memberId')) {
            $MemberFilter = new MemberFilter();
            $filters = $MemberFilter->getbyId($this->session->get('memberId'));
            if (isset($filters['category']['value'])) {
                $response['member_categories'] = $filters['category']['value'];
            }
            $member = new Member();
            $member = $member::findFirst($this -> session -> get('memberId'));
            
            if ($member -> location) {
            	$response['member_location_latitudeMin'] = $member -> location -> latitudeMin;
            	$response['member_location_latitudeMax'] = $member -> location -> latitudeMax;
            	$response['member_location_longitudeMin'] = $member -> location -> longitudeMin;
            	$response['member_location_longitudeMax'] = $member -> location -> longitudeMax;
            	$response['member_location_city'] = $member -> location -> city;
            	$response['member_location_country'] = $member -> location -> country;
            } else {
            	$response['member_location_latitudeMin'] = $this -> session -> get('location') -> latitudeMin;
            	$response['member_location_latitudeMax'] = $this -> session -> get('location') -> latitudeMax;
            	$response['member_location_longitudeMin'] = $this -> session -> get('location') -> longitudeMin;
            	$response['member_location_longitudeMax'] = $this -> session -> get('location') -> longitudeMax;
            	$response['member_location_longitudeMax'] = $this -> session -> get('location') -> city;
            	$response['member_location_longitudeMax'] = $this -> session -> get('location') -> country;
            }
        } else {
            $response['errors'] = true;
            $response['error_msg'] = 'Personalize search only for logged users. Please <a href="#" onclick="return false;" class="fb-login-popup">login via Facebook</a>';
        }

        $this->sendAjax($response);
    }

    /**
     * @Route("/member/login", methods={'get'})
     * @Acl(roles={'guest', 'member'});
     */
    public function loginAction()
    {
        $form = new LoginForm();
        $this -> view -> form = $form;
        $this->view->pick('member/login');
    }

    
    /**
     * @Route("/member/task-fb", methods={'post'})
     * @Acl(roles={'member'});
     */
    public function addCronTaskAction()
    {
    	$response['error'] = '';
    	
    	if ((new Cron()) -> createUserTask(true)) {
    		$response['status'] = 'OK';
    	} else {
    		$response['status'] = 'FAIL';
    	}
    	
    	$this -> sendAjax($response); 
    }
    
    
    /**
     * @Route("/member/annihilate", methods={'post','get'})
     * @Acl(roles={'member'});
     */
    public function annihilateAction()
    {
    	$newTask = new \Objects\Cron();
    	$params = ['member_id' => $this -> session -> get('memberId')];
    	$task = ['name' => 'clear_member_cache',
		    	 'parameters' => serialize($params),
		    	 'state' => 0,
		    	 'member_id' => $this -> session -> get('memberId'),
		    	 'hash' => time()];
    	$newTask -> assign($task);
    	$newTask -> save(); 
    	
    	$member = Member::findFirst($this -> session -> get('memberId'));
		$member -> fullDelete();

		$this -> response -> redirect('/auth/logout');
    }
}