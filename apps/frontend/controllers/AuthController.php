<?php

namespace Frontend\Controllers;

use Core\Logger;
use Frontend\Form\SignupForm,
    Frontend\Form\LoginForm,
    Frontend\Form\RestoreForm,
    Frontend\Form\ResetForm,
    Frontend\Models\Member,
    Frontend\Models\Cron,
    Frontend\Models\Location,
    Frontend\Models\MemberNetwork,
    Frontend\Events\MemberListener,
    Core\Auth,
    Core\Acl,
    Core\Utils as _U;


class AuthController extends \Core\Controller
{
    public function onConstruct()
    {
        $this -> eventsManager -> attach('App.Auth.Member', new MemberListener());
    }
    
    /**
     * @Route("/motologin", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function motologinAction()
    {
    }
    

    /**
     * @Route("/auth/login", methods={"GET", "POST"})
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

                if (!$member || !$this->security->checkHash($pass, $member->pass)) {
                    if (!$this->request->isAjax()) {
                        $this->setFlash('Your email or password are incorrect', 'error');
                    } else {
                        echo json_encode(array('error' => 'Your email or password are incorrect'));
                        exit();
                    }
                } else {
                    $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $member);
                    $this->eventsManager->fire('App.Auth.Member:deleteCookiesAfterLogin', $this);

                    if (!$this->request->isAjax()) {
                        $this -> response -> redirect('');
                    } else {
                        echo json_encode(array('success' => 'true'));
                        exit();
                    }
                }
            }
        }

        $this -> view -> form = $form;
    }


    /**
     * @Route("/auth/signup", methods={"GET", "POST"})
     * @Acl(roles={'guest'});
     */
    public function signupAction()
    {
        $form = new SignupForm();

        if ($this -> request -> isPost()) {
            if ($form -> isValid($this -> request -> getPost())) {
                $member = new Member();
                $member -> assign(array(
                        'email' => $this -> request -> getPost('email', 'email'),
                        'pass' => $this -> security -> hash($this -> request -> getPost('password')),
                        'role' => Acl::ROLE_MEMBER,
                        'location_id' => $this -> session -> get('location') -> id
                    ));

                if (!$member -> validation()) {
                    $this -> flash -> error($member -> getMessages());
                    $this -> view -> form = $form;
                    return;
                }

                if ($member -> save()) {
                    $this -> eventsManager -> fire('App.Auth.Member:registerMemberSession', $this, $member);
                    $this -> response -> redirect('');
                } 
                    
                $this -> flash -> error($member -> getMessages());
            }
        }
        $this -> view -> form = $form;
    }



    /**
     * @Route("/auth/fblogin", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function fbloginAction()
    {
    	$uid = $this -> request -> getPost('uid', 'string');
        $access_token = $this -> request -> getPost('access_token', 'string');
        $access_type = $this -> request -> getPost('access_type', 'string');

        if (!empty($access_token)) {
        	$this -> session -> set('user_token', $access_token);
        	$this -> session -> set('user_fb_uid', $uid);
        	
        	if ($access_type == 'sync') {
				(new Cron()) -> createUserTask();
        	} else {
        		$memberNetwork = MemberNetwork::findFirst('account_uid = "' . $uid . '"');
        		 
        		if ($memberNetwork) {
        			$this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $memberNetwork -> member);
        			(new Cron()) -> createUserTask();
        		}
        		$this -> session -> set('role', Acl::ROLE_MEMBER);
        		$this -> eventsManager -> fire('App.Auth.Member:deleteCookiesAfterLogin', $this);
        	}
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
     * @Route("/auth/fbregister", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function fbregisterAction()
    {
        $userData =  $this -> request -> getPost();
        isset($this -> getDI() -> get('facebook_config') -> facebook -> version) ? $fbVesrion = $this -> getDI() -> get('facebook_config') -> facebook -> version : $fbVersion = 'v2.2';
        $res = [];
        
        if (!$this -> session -> has('member')) {
       		$memberNetwork = MemberNetwork::findFirst('account_id = "' .  $userData['uid'] . '"');
       		if ($memberNetwork) {
       			$this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $memberNetwork -> member);
        		(new Cron()) -> createUserTask();
       		}
        } elseif ($this -> session -> has('member') && !isset($this -> session -> get('member') -> network)) {
        	(new MemberNetwork()) -> addMemberNetwork($this -> session -> get('memberId'), $userData['uid'], $userData['user_name']);
        }
        
        
        if (!$this -> session -> has('member')) {
            $member = new Member();
            $locationByIp = $this->session->get('location');
            if (isset($userData['locationLat']) && isset($userData['locationLng']) && $locationByIp) {
                $locationByFb = (new Location()) -> createOnChange(['latitude' => $userData['locationLat'], 'longitude' => $userData['locationLng']]);

                if ($locationByIp -> id != $locationByFb -> id) { 
                    $this -> session -> set('location_conflict', true);
                    $this -> session -> set('location_conflict_profile_flag', true);
                }
            }

            $member -> assign(array(
                    'pass' => $this -> security -> hash(rand(0, 500) . '+' . microtime()), //md5(rand(0, 500) . '+' . microtime()),
                    'role' => Acl::ROLE_MEMBER,
                    'location_id' => $locationByIp -> id,
                    'auth_type' => 'facebook',
                    'logo' => $userData['logo']
                ));
            if (isset($userData['email'])) {
            	$member -> assign(['email' => $userData['email']]);
            }
            if (isset($userData['first_name'])) {
            	if (isset($userData['last_name'])) { 
            		$member -> assign(['name' => $userData['first_name'] . ' ' . $userData['last_name']]);
            	} else {
            		$member -> assign(['name' => $userData['first_name']]);
            	}
            } else {
            	$member -> assign(['name' => $userData['user_name']]);
            }
            
            if ($userData['user_name'] == '' || empty($userData['user_name'])) {
            	if ($userData['email']) {
            		$userData['user_name'] = $userData['email'];
            	}
            }
            
            if ($member -> save()) {
                $this->eventsManager->fire('App.Auth.Member:afterPasswordSet', $this, $member);

                $memberNetwork = (new MemberNetwork()) -> addMemberNetwork($member, $userData['uid'], $userData['user_name']);
                
                $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $member);
                $this->eventsManager->fire('App.Auth.Member:checkLocationMatch', $this, array(
                    		'member' => $member,
                    		'uid' => $userData['uid'],
                    		'token' => $userData['token']));
                (new Cron()) -> createUserTask();                
            }
        }

        $res['status'] = 'OK';
        echo json_encode($res);
    }
    
    
    /**
     * @Route("/auth/fbauthresponse{request}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function fbauthresponseAction()
    {
    	$this -> view -> pick('auth/fbresponse');
    }
    
    /**
     * @Route("/auth/fbpermissions", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function fbpermissionsAction()
    {
    	$data = $this -> request -> getPost();
    	$res = [];
    
    	if (!empty($data)) {
    		$memberNetwork = MemberNetwork::findFirst('member_id = "' . $this -> session -> get('memberId') . '"');
    
    		if ($memberNetwork) {
    			$memberNetwork -> permission_base = $data['permission_base'];
    			$memberNetwork -> permission_publish = $data['permission_publish'];
    			$memberNetwork -> permission_manage = $data['permission_manage'];
    			$memberNetwork -> update();
    			
    			$this -> session -> set('permission_base', $data['permission_base']);
    			$this -> session -> set('permission_publish', $data['permission_publish']);
    			$this -> session -> set('permission_manage', $data['permission_manage']);
    			
    			$res['status'] = 'OK';
    			echo json_encode($res);
    		} else {
    			$res['status'] = 'ERROR';
    			$res['message'] = 'No such FB user';
    			$res['memberSessionId'] = $this -> session -> get('memberId');
    			echo json_encode($res);
    		}
    	} else {
    		$res['status'] = 'ERROR';
    		$res['message'] = 'Permissions is empty';
    		echo json_encode($res);
    	}
    }


    /**
     * @Route("/auth/restore", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function restoreAction()
    {
        $form = new RestoreForm();

        if ($this -> request -> isPost()) {
            $email = $this -> request -> getPost('email', 'email');
            if ($form -> isValid($this -> request -> getPost())) {
                $member = Member::findFirst('email = "'.$email.'"');
                if (!$member) {
                    $this -> view -> setVar('flashMsgText', 'No such email in database');
					$this -> view -> setVar('flashMsgType', 'error');
                    $this -> view -> form = $form;
                    return false;
                }
                $resetUri =  md5(rand(0, 500) . '+' . $member -> id . '+' . microtime());
                $this -> session -> set('reset_uri', $resetUri);
                $this -> session -> set('reset_member', $member);
                $resetLink = $_SERVER['SERVER_NAME'] . '/reset/' . $resetUri;

                $template = "Here is your link for new password: http://" . $resetLink. "\n\nDon't lose it again";
                $subject = 'EventWeekly::Reset password';
                $to = $email;

                $message = $this->di->get('mailMessage');
                $message->setSubject($subject)
	                    ->setFrom(array('support@eventweekly.com' => 'EW Support'))
	                    ->setTo(array($to))
	                    ->setBody($template);

                $mailer = $this->di->get('mailEmailer');
                $res = $mailer->send($message);
                Logger::log('Email send result: ', \Phalcon\Logger::DEBUG);
                Logger::log($res, \Phalcon\Logger::DEBUG);

                //if (mail($to, $subject, $template)) {
                    $this -> view -> pick('auth/reseted');
                //}
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
        /*if ($hash) {
            if ($hash == $this -> session -> get('reset_uri')) { */
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

                                $this -> response -> redirect('/auth/login');
                            }
                        }
                    }
                }
                $this -> view -> form = $form;
            //} else {
//				$this -> view -> setVar('flashMsgText', 'Your session is deprecated or you has logged from another device');
				//$this -> view -> setVar('flashMsgType', 'error');
            //}
        //} 
    }

   /**
     * @Route("/auth/checkunique", methods={"POST"})
     * @Acl(roles={'guest','member'});
     */
    public function checkuniqueAction()
    {
        $data =  $this -> request -> getPost();
        $response['status'] = 'ERROR';

        if ($data['email']) {
            $isExists = Member::findFirst('email = "' . $data['email'] . '"');
      
            if ($isExists) {
                $response['message'] = 'User already exists';
            } else {
                $response['status'] = 'OK';
            }
        }

        echo json_encode($response); 
    }

    /**
     * @Route("/auth/logout", methods={"GET", "POST"})
     * @Acl(roles={'guest','member'});
     */
    public function logoutAction()
    {
		$this -> session -> destroy();
		return $this -> response -> redirect('/');
    }
    

    private function _registerMemberSession($params) {
    	$this -> session -> set('member', $params);
    	$this -> session -> set('role', $params -> role);
    	$this -> session -> set('memberId', $params -> id);
    	
    	return;
    }
}
