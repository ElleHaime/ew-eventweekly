<?php

namespace Frontend\Controllers;

use Frontend\Form\SignupForm,
	Frontend\Form\LoginForm,
	Frontend\Form\RestoreForm,
	Frontend\Form\ResetForm,
	Frontend\Models\Member,
	Frontend\Models\MemberNetwork,
	Core\Auth,
	Core\Acl,
	Core\Utils as _U;

/**
 * @RouteRule(useCrud = false)
 */
class AuthController extends \Core\Controller
{
	/**
	 * @Route("/login", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   
	 */
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

                $this->afterLogin();

				$this -> response -> redirect('map');
				
			} else {
				$this -> response -> setStatusCode(401, 'Unauthorized')
								  -> setContent('Not authorized')
								  -> send();
			}
		}
   		$this -> view -> form = $form;
    }
	

	
	/**
	 * @Route("/signup", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   
	 */
    public function signupAction()
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

				$email = $this -> request -> getPost('email');
				$password = $this -> request -> getPost('password');
				$location = $this -> session -> get('location');
				$member -> assign(array(
					'email' => $email,
					'pass' => $this -> security -> hash($password),
					'role' => Acl::ROLE_MEMBER,
					'location_id' => $location -> id 
				));
	
				if ($member -> save()) {
					$this -> _registerMemberSession($member);
					$this -> response -> redirect('map');
				} else {
					echo 'Sad =/'; die();
				}
				$this -> flash -> error($member -> getMessages());
			} 
		} 
		$this -> view -> form = $form;
    }



    /**
     * @Route("/fblogin", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});     
     */
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

            $this -> afterLogin();

		    $res['status'] = 'OK';
		    $res['message'] = $access_token;
		    echo json_encode($res);
	    } else {
		    $res['status'] = 'ERROR';
		    $res['message'] = 'Token is empty';
		    echo json_encode($res);
	    }
    }

    /**
     * @Route("/fbregister", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   
     */
    public function fbregisterAction()
    {
    	if (!$this -> session -> has('member')) {
	    	$userData =  $this -> request -> getPost();
	    	$member = new Member();
	    	
	    	if (!isset($userData['location']) || empty($userData['location'])) {
	    		$memberLocation = $this -> session -> get('location');
		    } else {
			    $memberLocation = $this -> locator -> createOnChange($userData['location']);
		    }

			$member -> assign(array(
					'pass' => md5(rand(0, 500) . '+' . microtime()),
					'email' => $userData['email'],
					'role' => Acl::ROLE_MEMBER,
					'location_id' => $memberLocation -> id,
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

    
    /**
     * @Route("/restore", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});     
     */
    public function restoreAction()
    {
    	$form = new RestoreForm();

    	if ($this -> request -> isPost()) {
    		$email = $this -> request -> getPost('email', 'string');
    		
    		if ($form -> isValid($this -> request -> getPost())) {
    			$member = Member::findFirst(array('email = ?0',
    											  'bind' => (array)$email));
    			if (!$member) {
    				$this -> flash -> error('Use with such email doesn\'t exists');
    				$this -> view -> form = $form;
    				return false;
    			}
    			$resetUri =  md5(rand(0, 500) . '+' . $member -> id . '+' . microtime());
				$this -> session -> set('reset_uri', $resetUri);
				$this -> session -> set('reset_member', $member);
				$resetLink = $_SERVER['SERVER_NAME'] . '/reset/' . $resetUri; 
				
				$template = "Here is your link for new password: http://" . $resetLink. "\n\nDon't lose it again";
				$subject = 'Reset password';
				$to = $email;

				if (mail($to, $subject, $template)) {
					$this -> view -> pick('auth/reseted'); 
				} 
    		}
    	}
    	
    	$this -> view -> form = $form;
    }

    
    /**
     * @Route("/reset/{hash}", methods={"GET"})
     * @Acl(roles={'guest', 'member'});
     */    
    public function resetAction($hash = false)
    {
		if ($hash) {
			if ($hash == $this -> session -> get('reset_uri')) {
				$form = new ResetForm();

				if ($this -> request -> isPost()) {
					$password = $this -> request -> getPost('password', 'string');
	        
					if ($form -> isValid($this -> request -> getPost())) {
						if ($this -> session -> has('reset_member')) {
							$member = $this -> session -> get('reset_member');
							$member -> assign(array(
								'pass' => $this -> security -> hash($this -> request -> getPost('password'))
							));
							if ($member -> save()) {
								$this -> session -> remove('reset_uri');
								$this -> session -> remove('reset_member');
			
								$this -> response -> redirect('login');
							}
						}
					}
				}
				
				$this -> view -> form = $form;				
			}
		}
	}
    
    
    /**
     * @Route("/logout", methods={"GET", "POST"})
	 * @Acl(roles={'guest','member'});     
     */
    public function logoutAction()
    {
		$this -> session -> destroy();
		return $this -> response -> redirect();
    }
    

    private function _registerMemberSession($params) {
    	$this -> session -> set('member', $params);
    	$this -> session -> set('role', $params -> role);
    	$this -> session -> set('memberId', $params -> id);
    	
    	return;
    }

    private function afterLogin() {
        $this -> cookies -> get('lastLat') -> delete();
        $this -> cookies -> get('lastLng') -> delete();
    }
}
