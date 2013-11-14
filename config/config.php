<?php 

if (!defined('ROOT_FRONT')) {
	define('ROOT_FRONT', ROOT_APP . 'apps/frontend/');
}

if (!defined('ROOT_BACK')) {
	define('ROOT_BACK', ROOT_APP . 'apps/backend/');
}


$cfg_settings = array(
	'database' => array(
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'root',
		'dbname' => 'ew',
		'charset'   =>'utf8'
	),
	
	'application' => array(
		'debug' => true,
		'baseUri' => '/',
		'defaultModule' => 'frontend',
		'defaultNamespace' => 'Frontend\Controllers',
		'defaultController' => 'index',
		'defaultAction' => 'index',
		'modulesDir' => ROOT_APP . 'apps/',
		'logger' => array(
			'enabled' => true,
			'path' => ROOT_APP . 'var/logs/',
			'format' => '[%date%][%type%] %message%',
			'adapter' => 'File'
		),
		'views' => array (
			'compiledPath' => ROOT_APP . 'var/compiled/',
			'compiledExtension' => '.compiled'
		),
		'cache' => array(
			'lifetime' => '86400',
			'prefix' => 'pe_',
			'adapter' => 'Memcached',
			'cacheDir' => ROOT_APP . 'var/cache/data/',
		),
		'namespaces' => array(
			'Core' => ROOT_APP . 'apps/library/Core/',
			'Core\Acl' => ROOT_APP . 'apps/library/Core/Acl/',
			'Core\Controllers' => ROOT_APP . 'apps/library/Core/Controller/',
			'Core\Form\Element' => ROOT_APP . 'apps/library/Core/Form/Element/',
			'Objects' => ROOT_APP . 'apps/objects/',
			'Thirdparty\Geo' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
			'Thirdparty\Facebook' => ROOT_APP . 'apps/library/Thirdparty/Facebook/',
		),
		'mailer' => array(
			//'path' => ROOT_LIB . 'Mail/Swift/swift_required.php'
		),
		'geo' => array(
			'path' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
		)
	),
	
	'modules' => array(
		
		'backend' => array(
			'name' => 'backend',
			'namespaces' => array(
				'Backend\Controllers' => ROOT_BACK . 'controllers/',
				'Backend\Models' => ROOT_BACK . 'models/',
				'Backend' => ROOT_BACK,
			),
			'bootstrapNs' => 'Backend\Bootstrap',
			'bootstrapPath' => ROOT_BACK . 'Bootstrap.php',
			'defaultNameSpace' => 'Backend\Controllers', 
		),
			
		'frontend' => array(
			'name' => 'frontend',					
			'namespaces' => array(
				'Frontend\Controllers' => ROOT_FRONT . 'controllers/',
				'Frontend\Models' => ROOT_FRONT . 'models/',
				'Frontend' => ROOT_FRONT,
				'Frontend\Form' => ROOT_FRONT . 'form/',
			),
			'bootstrapNs' => 'Frontend\Bootstrap',
			'bootstrapPath' => ROOT_FRONT . 'Bootstrap.php',
			'defaultNameSpace' => 'Frontend',
			'formNamespace' => 'Frontend\Form',
		)
	),
	
	'router' => array(
		'/' => array(
			'module' => 'frontend',
			'controller' => 'index',
			'action' => 'index'
		),
		'registration' => array(
			'module' => 'frontend',
			'controller' => 'auth',
			'action' => 'register'
		),
		'home' => array(
			'module' => 'frontend',
			'controller' => 'member',
			'action' => 'index'
		),
		'profile' => array(
			'module' => 'frontend',
			'controller' => 'member',
			'action' => 'list'
		),
		'profile/edit' => array(
			'module' => 'frontend',
			'controller' => 'member',
			'action' => 'edit'
		),
		'event' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'event'
		),
		'event/add' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'edit'
		),
		'event/edit/:int' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'edit',
			'eid' => 1
		),
		'event/manage' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'manage'
		),
		'event/list' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'list'
		),
		'campaign' => array(
			'module' => 'frontend',
			'controller' => 'campaign',
			'action' => 'campaign'
		),
		'campaign/add' => array(
			'module' => 'frontend',
			'controller' => 'campaign',
			'action' => 'edit'
		),
		'campaign/edit/:int' => array(
			'module' => 'frontend',
			'controller' => 'campaign',
			'action' => 'edit',
			'campaign' => 1
		),
		'campaign/delete/:int' => array(
			'module' => 'frontend',
			'controller' => 'campaign',
			'action' => 'delete',
			'campaign' => 1
		),
		'campaign/list' => array(
			'module' => 'frontend',
			'controller' => 'campaign',
			'action' => 'list'
		),			
		'map' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'map'
		),
		'login' => array(
			'module' => 'frontend',
			'controller' => 'auth',
			'action' => 'login'
		),
		'fblogin' => array(
			'module' => 'frontend',
			'controller' => 'auth',
			'action' => 'fblogin'
		),			
		'fbregister' => array(
			'module' => 'frontend',
			'controller' => 'auth',
			'action' => 'fbregister'
		),
		'logout' => array (
			'module' => 'frontend',
			'controller' => 'auth',
			'action' => 'logout'
		),
		'search' => array (
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'search'
		),
		'profile/refresh' => array(
			'module' => 'frontend',
			'controller' => 'member',
			'action' => 'refresh'
		),
		'event/events' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'events'
		),
		'event/show/:int' => array(
			'module' => 'frontend',
			'controller' => 'event',
			'action' => 'show',
			'eid' => 1
		),			
		'admin' => array (
			'module' => 'backend',
			'controller' => 'index',
			'action' => 'index'
		),				
	)
);
