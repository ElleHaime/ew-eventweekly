<?php

namespace Frontend\Controllers;

use Core\Logger;
use Frontend\Form\SignupForm,
    Frontend\Form\LoginForm,
    Frontend\Form\RestoreForm,
    Frontend\Form\ResetForm,
    Frontend\Models\Member,
    Frontend\Models\EventMemberCounter,
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

                if (!$member || !$this->security->checkHash($pass, $member->pass)) {
                    if (!$this->request->isAjax()) {
                        $this->setFlash('Wrong login credentials!', 'error');
                    } else {
                        echo json_encode(array('error' => 'Wrong login credentials!'));
                        exit();
                    }
                } else {
                    $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $member);
                    $this->eventsManager->fire('App.Auth.Member:setEventsCounters', $this, $member);
                    $this->eventsManager->fire('App.Auth.Member:deleteCookiesAfterLogin', $this);

                    if (!$this->request->isAjax()) {
                        $this -> response -> redirect('/map');
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
     * @Route("/signup", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
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
                	$memberCounter = new EventMemberCounter();
                	$memberCounter -> assign(['member_id' => $member -> id,
				                			  'events_liked' => 0,
				                			  'events_going' => 0,
				                			  'events_friends' => 0,
				                			  'events_created' => 0]);
                	$memberCounter -> save();
                	 
                    $this -> eventsManager -> fire('App.Auth.Member:registerMemberSession', $this, $member);
                    $this -> eventsManager -> fire('App.Auth.Member:setEventsCounters', $this, $member);
                    $this -> response -> redirect('/map');
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
            $memberNetwork = MemberNetwork::findFirst('account_uid = "' . $uid . '"');

            if ($memberNetwork) {
                $this->eventsManager->fire('App.Auth.Member:registerMemberSession', $this, $memberNetwork -> member);
                $this->eventsManager->fire('App.Auth.Member:setEventsCounters', $this, $memberNetwork -> member);
            }
           
            $this -> session -> set('user_token', $access_token);
            $this -> session -> set('user_fb_uid', $uid);
            $this -> session -> set('role', Acl::ROLE_MEMBER);

            $this -> eventsManager -> fire('App.Auth.Member:deleteCookiesAfterLogin', $this);

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
        $userData =  $this -> request -> getPost();

        if (!$this -> session -> has('member')) {
            $member = new Member();

            $locationByIp = $this->session->get('location');

            if (isset($userData['location']) || !empty($userData['location'])) {
                $locationByFb = $userData['location'];

                if ((strtolower($locationByFb['country']) != strtolower($locationByIp->country)) 
                        || (strtolower($locationByFb['city']) != strtolower($locationByIp->city))) 
                {
                    $this->session->set('location_conflict', true);
                    $this->session->set('location_conflict_profile_flag', true);
                }
            }

            $memberLocation = $locationByIp;

            $member -> assign(array(
                    'pass' => $this->security->hash(rand(0, 500) . '+' . microtime()), //md5(rand(0, 500) . '+' . microtime()),
                    'email' => $userData['email'],
                    'role' => Acl::ROLE_MEMBER,
                    'location_id' => $memberLocation -> id,
                    'name' => $userData['first_name'] . ' ' . $userData['last_name'],
                    'auth_type' => 'facebook',
                    'address' => $userData['address'],
                    'logo' => $userData['logo']
                ));

            if ($member -> save()) {

                $this->eventsManager->fire('App.Auth.Member:afterPasswordSet', $this, $member);

                $memberCounter = new EventMemberCounter();
                $memberCounter -> assign(['member_id' => $member -> id,
				               			  'events_liked' => 0,
				               			  'events_going' => 0,
				               			  'events_friends' => 0,
				               			  'events_created' => 0]);
                $memberCounter -> save();
                
                $memberNetwork = new MemberNetwork();
                $memberNetwork -> assign(array(
                        'member_id' => $member -> id,
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

                    $this->eventsManager->fire('App.Auth.Member:setEventsCounters', $this, $memberNetwork -> member);
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
            $email = $this -> request -> getPost('email', 'email');
            if ($form -> isValid($this -> request -> getPost())) {
                $member = Member::findFirst('email = "'.$email.'"');
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
        //$form = new ResetForm();

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

                                $this -> response -> redirect('/login');
                            }
                        }
                    }
                }

                $this -> view -> form = $form;
            }
        }

        //$this -> view -> form = $form;
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
     * @Route("/logout", methods={"GET", "POST"})
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
