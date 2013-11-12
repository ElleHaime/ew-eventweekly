<?php

namespace Frontend\Controllers;

use Frontend\Form\SignupForm,
	Frontend\Form\LoginForm,
	Frontend\Form\RestoreForm,
	Frontend\Models\Member,
	Frontend\Models\MemberNetwork,
	Frontend\Models\Location,
	Core\Auth,
	Core\Acl,
	Core\Utils as _U;


class AuthController extends \Core\Controller
{
    public function registerAction()
    {
		$form = new SignupForm();

		if ($this -> request -> isPost()) {
			if ($form -> isValid($this -> request -> getPost())) {
				$member = new Member();
				if (!$member -> validation()) {
					$this -> flash -> error("Email already exists");
					$this -> view -> form = $form;
					return;
				}
				$locations = new Location();
				$memberLocation = $locations -> createOnChange($this -> geo -> getUserLocation());

				$email = $this -> request -> getPost('email');
				$password = $this -> request -> getPost('password');
				$member -> assign(array(
					'email' => $email,
					'pass' => $this -> security -> hash($password),
					'role' => Acl::ROLE_MEMBER,
					'location_id' => $memberLocation 
				));
	
				if ($member -> save()) {
					$this -> _registerMemberSession($member);
					$this -> response -> redirect('home');
				} else {
					echo 'Sad =/'; die();
				}
				
				$this -> flash -> error($member -> getMessages());
			} 
		} 

		$this -> view -> setVar('location', $this -> geo -> getUserLocation(array('city', 'country')));
		$this -> view -> form = $form;
    }


    public function loginAction()
    {
    	$form = new LoginForm();
	
		if ($this -> request -> isPost()) {
			$email = $this -> request -> getPost('email', 'string');
			$pass = $this -> request -> getPost('password', 'string');
					
			if ($form -> isValid($this -> request -> getPost())) {
				$member = Member::findFirst(array('email = ?0',
												   'bind' => (array)$email));
				
				if (!$member) {
					$this -> flash -> error('No such member');
					$this -> view -> form = $form;
					return false;
				}
				if (!$this -> security -> checkHash($pass, $member -> pass)) { 
					$this -> flash -> error('Incorrect password');
					return false;
				}
				
				$this -> _registerMemberSession($member);
				$this -> response -> redirect('home');
				
			} else {
				$this -> response -> setStatusCode(401, 'Unauthorized')
								  -> setContent('Not authorized')
								  -> send();
			}
		}

		$this -> view -> setVar('location', $this -> geo -> getUserLocation(array('city', 'country')));
    	$this -> view -> form = $form;
    }


    public function fbloginAction()
    {
		$access_token = $this -> request -> getPost('access_token', 'string');
		$uid = $this -> request -> getPost('uid', 'string');

	    if (!empty($access_token)) {
	    	$memberNetwork = MemberNetwork::findFirst(array('account_uid = "' . $uid . '"'));

	    	if ($memberNetwork) {
	    		$this -> _registerMemberSession($memberNetwork -> member);
	    	}
		    $this -> session -> set('user_token', $access_token);
		    $this -> session -> set('role', Acl::ROLE_MEMBER);

		    $res['status'] = 'OK';
		    $res['message'] = $access_token;
		    echo json_encode($res);
	    } else {
		    $res['status'] = 'ERROR';
		    $res['message'] = 'Token is empty';
		    echo json_encode($res);
	    }
    }

    public function fbregisterAction()
    {
    	if (!$this -> session -> has('member')) {
	    	$userData =  $this -> request -> getPost();
	    	$member = new Member();
	    	$location = new Location();
	    	if (!isset($userData['location']) || empty($userData['location'])) {
	    		$memberLocation = $location -> createOnChange($this -> geo -> getUserLocation());
		    } else {
			    if (is_array($userData['location']))
			    {
				    $location = $userData['location']['country'].', '.$userData['location']['city'];
			    }
			    $memberLocation = $location -> createOnChange($location);
		    }

			$member -> assign(array(
					'email' => $userData['email'],
					'role' => Acl::ROLE_MEMBER,
					'location_id' => $memberLocation,
					'name' => $userData['first_name'] . ' ' . $userData['last_name'],
					'auth_type' => 'facebook',
					'address' => $userData['address'],
					'logo' => $userData['logo']
			));

			if ($member -> save()) {
				$memberNetwork = new MemberNetwork();
			
				$memberNetwork -> assign(array(
					'member_id' => $member -> id,
					'network_id' => 1,
					'account_uid' => $userData['uid'],
					'account_id' => $userData['username']
				));

				if ($memberNetwork -> save()) {
					$this -> _registerMemberSession($member);
				} else {
					echo 'Sad =/'; die();
				}
			}

		}
		
		$res['status'] = 'OK';
		echo json_encode($res);
    }


    public function restoreAction()
    {
    	$form = new RestoreForm();

    	if ($this -> request -> isPost()) {
    		if ($form -> isValid($this -> request -> getPost())) {
    			
    		}
    	}

    	$this -> view -> form = $form;
    }

    public function logoutAction()
    {
    	$this -> session -> destroy();
    	$this -> response -> redirect();
    }
    
    private function _registerMemberSession($params) {
    	$this -> session -> set('member', $params);
    	$this -> session -> set('role', $params -> role);
    	$this -> session -> set('memberId', $params -> id);
    	
    	return;
    }

}