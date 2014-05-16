<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\Member,
    Frontend\Models\Location,
    Frontend\Models\Tag,
    Frontend\Form\ChangePassForm,
    Frontend\Form\MemberForm,
    Frontend\Form\LoginForm,
    Frontend\Models\MemberNetwork,
	Frontend\Models\EventLike,
	Frontend\Models\EventMember,
	Frontend\Models\EventMemberFriend,
	Frontend\Models\EventMemberCounter;


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

        $tagIds = '';
        if ( isset($member_categories['tag']['value']) ) {
            $tagIds = implode(',', $member_categories['tag']['value']);
        }

		$this->view->setVars(array(
                'member', $list,
                'categories' => Category::find()->toArray(),
                'tags' => Tag::find()->toArray(),
                'tagIds' => $tagIds,
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
        if (!empty($postData)) {
            $elemExists = function($elem) use (&$postData) {
                if (!is_array($postData[$elem])) {
                    $postData[$elem] = trim(strip_tags($postData[$elem]));
                }
                return (array_key_exists($elem, $postData) && !empty($postData[$elem]));
            };

            $MemberFilter = new MemberFilter();

            if (!empty($postData['category']) && $elemExists('category')) {

                $toSave = array(
                    'member_id' => $Member->id,
                    'key' => 'category',
                    'value' => $postData['category']
                );

                if (!empty($postData['member_filter_category_id']) && $elemExists('member_filter_category_id')) {
                    $toSave['id'] = $postData['member_filter_category_id'];
                }

                $MemberFilter->save($toSave);
                
                // add unselected tags to full categories
                !empty($postData['tagIds']) ? $tagDiff = explode(',', $postData['tagIds']) : $tagDiff = [];
                $isTagSetted = [];
                $additionalCatTags = [];
                $additionalTags = [];
                foreach ($postData['category'] as $key => $val) { 
                	$curTags = Tag::find(['category_id = ' . $val]) -> toArray();
                	$isTagSetted[$val] = false;
                	if ($curTags) {
						while (list(, $tagOptions) = each($curTags)) {
	                		if (in_array($tagOptions['id'], $tagDiff)) {
	                			$isTagSetted[$val] = true;
	                			break;
	                		} else {
	                			$additionalCatTags[$val][] = $tagOptions['id']; 
	                		}
                		}
                	}
                	if ($isTagSetted[$val]) {
                		unset($additionalCatTags[$val]);
                	} else {
                		$additionalTags = array_merge($additionalTags, $additionalCatTags[$val]);
                	}
                }

        	} else {
                $filters = $MemberFilter->findFirst('member_id = '.$Member->id.' AND key = "category"');
                if ($filters) {
                    $filters->delete();
                }
            }
            
            $MemberFilter = new MemberFilter();
            if (!empty($postData['tagIds']) || !empty($additionalTags)) {
				!empty($postData['tagIds']) ? $allTags = $postData['tagIds'] . ',' . implode(',', $additionalTags) : $allTags = implode(',', $additionalTags); 	
            	
                $toSave = array(
                    'member_id' => $Member->id,
                    'key' => 'tag',
                    'value' => json_encode(array_filter(explode(',', $allTags)))
                );

                if (!empty($postData['recordTagId']) && isset($postData['recordTagId'])) {
                    $toSave['id'] = $postData['recordTagId'];
                }

                $MemberFilter->save($toSave);
            } else {
                $filters = $MemberFilter->findFirst('member_id = '.$Member->id.' AND key = "tag"');
                if ($filters) {
                    $filters->delete();
                }
            }
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

        $postData = $this -> request-> getPost();

        $newLoc = new Location();
        $Location = $newLoc -> createOnChange(array('latitude' => $postData['lat'], 'longitude' => $postData['lng']));
        $id = $Location->id;

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

            $this->cookies->get('lastLat')->delete();
            $this->cookies->get('lastLng')->delete();
            $this->cookies->get('lastCity')->delete();

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
     * @Route("/member/link-fb", methods={'post'})
     * @Acl(roles={'member'});
     */
    public function linkToFBAccountAction()
    {
        $response = [
            'errors' => false
        ];

        $userData = $this->request->getPost();

        if ($this->session->has('member')) {
            $member = $this->session->get('member');

            $memberNetwork = new MemberNetwork();

            $memberNetwork -> assign(array(
                'member_id' => $member->id,
                'network_id' => 1,
                'account_uid' => $userData['uid'],
                'account_id' => $userData['username']
            ));

            if ($memberNetwork -> save()) {
                $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $member);
                $this->eventsManager->fire('App.Auth.Member:checkLocationMatch', $this, array(
                    'member' => $member,
                    'uid' => $userData['uid'],
                    'token' => $userData['token']
                ));

                $this -> session -> set('user_fb_uid', $userData['uid']);
                $this->session->set('user_token', $userData['token']);
                $this->session->set('acc_synced', true);
                $this -> view -> setVar('acc_external', $memberNetwork);
            }
        }

        echo json_encode($response);
    }

    /**
     * @Route("/member/sync-fb", methods={'post'})
     * @Acl(roles={'member'});
     */
    public function syncToFBAccountAction()
    {
        $response = [
            'errors' => false
        ];

        $userData = $this->request->getPost();

        if ($this->session->has('member')) {
            $member = $this->session->get('member');

            $memberNetwork = MemberNetwork::findFirst('member_id = ' . $member->id . ' AND account_uid = ' . $userData['uid']);

            if ($memberNetwork->id) {
                $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $member);
                $this->eventsManager->fire('App.Auth.Member:checkLocationMatch', $this, array(
                    'member' => $member,
                    'uid' => $userData['uid'],
                    'token' => $userData['token']
                ));

                $this -> session -> set('user_fb_uid', $userData['uid']);
                $this->session->set('user_token', $userData['token']);
                $this->session->set('acc_synced', true);
                $this->view->setVar('acc_external', $memberNetwork);
                
                if ($this->session->has('user_token') && $this->session->has('user_fb_uid')) {
                	$newTask = false;
                
                	$taskSetted = \Objects\Cron::find(array('member_id = ' . $member -> id  . ' and name =  "extract_facebook_events"'));
                	if ($taskSetted -> count() > 0) {
                		 $tsk = $taskSetted -> getLast();
                		if (time()-($tsk -> hash) > 300) {
                			$newTask = new \Objects\Cron();
                		}
                	} else {
                		$newTask = new \Objects\Cron();
                	}
                
                	if ($newTask) {
                		$params = ['user_token' => $this -> session -> get('user_token'),
			                		'user_fb_uid' => $this -> session -> get('user_fb_uid'),
			                		'member_id' => $this -> session -> get('memberId')];
                		
                		$task = ['name' => 'extract_facebook_events',
			                		'parameters' => serialize($params),
			                		'state' => 0,
			                		'member_id' => $this -> session -> get('memberId'),
			                		'hash' => time()];
                
                		$newTask -> assign($task);
                		$newTask -> save();
                	}
                }
            } else {
                $response = [ 'errors' => true ];
            }
        }

        echo json_encode($response);
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

		$this -> response -> redirect('/logout');
    }
}