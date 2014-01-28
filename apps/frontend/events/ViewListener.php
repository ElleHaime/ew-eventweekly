<?php
/**
 * Class ViewListener
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Frontend\Events;


class ViewListener {

    private $view = null;

    private $di = null;

    private $session = null;

    public function __construct($di)
    {
        $this->di = $di;
        $this->session = $di->getShared('session');
        $this->dispatcher = $di->getShared('dispatcher');
    }

    public function beforeRender($subject)
    {
        $this->view = $subject->getSource();
        $config = $this->di->getShared('config');

        $this->view->setVar('fbAppId', $config->application->facebook->appId);
        $this->view->setVar('fbAppSecret', $config->application->facebook->appSecret);

        $params = $this->dispatcher->getReturnedValue();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $param) {
                $this->view->setVar($key, $param);
            }
        }

        if ($this->session->has('flashMsgText')) {
            $this->view->setVar('flashMsgText', $this->session->get('flashMsgText'));
            $this->session->remove('flashMsgText');
        }

        if ($this->session->has('flashMsgType')) {
            $this->view->setVar('flashMsgType', $this->session->get('flashMsgType'));
            $this->session->remove('flashMsgType');
        }

        $this->view->setVar('location', $this->session->get('location'));

        $this->view->setVar('defaultEventLogo', $config->application->defaultLogo);
    }

} 