<?php 

namespace Core; 

use \Phalcon\Mvc\ModuleDefinitionInterface,
	\Phalcon\Mvc\Dispatcher,
	\Phalcon\Mvc\View,
	\Phalcon\DI,
	\Phalcon\Mvc\View\Engine\Volt,
	\Core\Auth,
	\Core\Mail,
	\Core\Geo,
	\Core\Acl,
    Frontend\Events\ViewListener;

abstract class Bootstrap implements ModuleDefinitionInterface
{
	protected $_moduleName 	= '';
	protected $_config		= null;
	protected $_loader		= null;
	protected $_di 			= null;
	

	public function __construct()
	{  
		$this -> _di = DI::getDefault();
		$this -> _config = $this -> _di -> getConfig();
	}
		
	public function registerAutoloaders()
	{
	}

	public function registerServices($di)
	{
		$this -> _initView($di);
		$this -> _initDispatcher($di);
		$this -> _initAuth($di);
		$this -> _initGeoApi($di);
		$this -> _initMail($di);
		$this -> _initSession($di);
		$this -> _initModels($di);
        $this -> initCoreTag($di);
        $this -> _initHttp($di);
 
	}

	protected function _initView($di)
	{
		$config = $this -> _config;
		$viewPath = $this -> getModuleDir() . '/views/';

        $that = $this;
	
		$di -> set('view',
			function() use ($config, $di, $viewPath, $that)
			{
				$view = new View();
				$view -> setViewsDir($viewPath);
 
				$view -> registerEngines(array(
					'.volt' => 					
						function ($view, $di) use ($config, $that)
						{
							$volt = new Volt($view, $di);
							$volt -> setOptions((array)$config -> application -> views);
							if ($config -> application -> debug) {
								//$volt -> setOptions(array('compileAlways' => true));
							}

                            $that->initViewFilters($volt);

                            $eventsManager = $di->getShared('eventsManager');

                            /*$eventsManager->attach('view:beforeRender', function($event, $view) use ($di) {
                                    $session = $di->getShared('session');

                                    if ($session->has('flashMsgText')) {
                                        $view->setVar('flashMsgText', $session->get('flashMsgText'));
                                        $session->remove('flashMsgText');
                                    }

                                    if ($session->has('flashMsgType')) {
                                        $view->setVar('flashMsgType', $session->get('flashMsgType'));
                                        $session->remove('flashMsgType');
                                    }

                                });*/

                            $eventsManager->attach('view:beforeRender', new ViewListener($di));

                            $view->setEventsManager($eventsManager);

							return $volt;
						},
					'.phtml' => 'Phalcon\Mvc\View\Engine\Volt'
				));
				return $view;
			}
		);
	}

	protected function _initDispatcher($di)
	{
		$config = $this -> _config;

		$di -> set('dispatcher',
			function() use ($config, $di) {
				$eventsManager = $di -> getShared('eventsManager');
				$security = new Acl($di);
				$eventsManager -> attach('dispatch', $security);

				$dispatcher = new Dispatcher();
				$dispatcher -> setEventsManager($eventsManager);

				return $dispatcher;
			}
		);
	}
	
	protected function _initModels($di)
	{
		$di -> set('modelsManager', function() {
			return new \Phalcon\Mvc\Model\Manager();
		});
	}
	
	protected function _initAuth($di)
	{
		$di -> set('auth', function() use ($di) {
			return new Auth($di);
		});
	}

	protected function _initSession($di)
	{
		$di -> set('session', function() {
			//$session = new \Phalcon\Session\Adapter\Files();
			$session = new \Core\Session();
			$session -> start();
			
			return $session;
		});
	}

	protected function _initMail($di)
	{
        $mailerPath = $this->_config->application->mailer->path;
        $config = $this->_config->application->mailer->config;
        require $mailerPath;

		$di->set('mailMessage', function() {
            return new \Core\Mail\Message();
		});

        $di->set('mailEmailer', function() use ($config) {
                return new \Core\Mail\Emailer($config);
            });

        /*$di -> set('mail', function() {
			return new Mail();
		});*/
	}

	protected function _initGeoApi($di)
	{
		if (!$di -> has('geo')) {
			$di -> set('geo', function() use ($di) {
				return new Geo($di);
			});
		}
	}
	
	public function getModule()
	{
		return $this -> _moduleName;
	}
	
	public function getModuleDir()
	{
		return $this -> _config -> application -> modulesDir . $this -> _moduleName;
	}

    public function initViewFilters($volt)
    {
        $compiler = $volt->getCompiler();

        $compiler->addFilter('hash', 'md5');

        $compiler->addFilter('truncate', function($value, $length = 30, $separator = '...') {
                $res = null;

                $value = $length[0]['expr']['value'];
                $length = $length[1]['expr']['value'];

                if (function_exists('mb_get_info')) {
                    $res = "mb_substr($value, 0, $length, 'utf-8') . \$sep = (strlen($value) > $length) ? '$separator' : ''";
                }else {
                    $res = "substr($value, 0, $length) . (strlen($value) > $length) ? '$separator' : ''";
                }

                if(empty($res)) {
                    $res = '\'\'';
                }

                return $res;
            });
    }

    public function initCoreTag($di)
    {
        $config = $this->_config;
        $di->set('tag', function() use ($config) {
                return new \Core\CoreTag($config);
            });
    }

    protected function _initHttp($di)
    {
        $di->set('http', function() use ($di) {
            return new \Core\Http($di);
        });
    }

}
