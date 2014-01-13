<?php 

if (!defined('ROOT_FRONT')) {
	define('ROOT_FRONT', ROOT_APP . 'apps/frontend/');
}

if (!defined('ROOT_BACK')) {
	define('ROOT_BACK', ROOT_APP . 'apps/backend/');
}


$cfg_settings = array(
	'application' => array(
		'debug' => true,
		'baseUri' => '',
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
			'Core\Traits' => ROOT_APP . 'apps/library/Core/Traits/',
			'Core\Form\Element' => ROOT_APP . 'apps/library/Core/Form/Element/',
			'Objects' => ROOT_APP . 'apps/objects/',
			'Thirdparty\Geo' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
			'Thirdparty\Facebook' => ROOT_APP . 'apps/library/Thirdparty/Facebook/',
			'Categoryzator' => ROOT_APP . 'apps/library/Thirdparty/Categoryzator/',
            'Thirdparty\MobileDetect' => ROOT_APP . 'apps/library/Thirdparty/MobileDetect/',
		),
		'mailer' => array(
			//'path' => ROOT_LIB . 'Mail/Swift/swift_required.php'
		),
		'geo' => array(
			'path' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
		),
        'uploadDir' => ROOT_APP . 'public/upload/'

    ),
	
	'modules' => array(
		
		'backend' => array(
            'enabled' => false,
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
            'enabled' => true,
			'name' => 'frontend',					
			'namespaces' => array(
				'Frontend\Controllers' => ROOT_FRONT . 'controllers/',
				'Frontend\Models' => ROOT_FRONT . 'models/',
				'Frontend' => ROOT_FRONT,
				'Frontend\Form' => ROOT_FRONT . 'form/',
				'Frontend\Events' => ROOT_FRONT . 'events/',
			),
			'bootstrapNs' => 'Frontend\Bootstrap',
			'bootstrapPath' => ROOT_FRONT . 'Bootstrap.php',
			'defaultNameSpace' => 'Frontend',
			'formNamespace' => 'Frontend\Form',
		)
	)
);
