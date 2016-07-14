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
        'timezone' => null,
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
			'adapter' => 'Memcache',
			'cacheDir' => ROOT_APP . 'var/cache/data/',
			'cachePrefix' => 'ewloc',
			'host' => 'localhost',
			'port' => 11211,
			'persistent' => false
		),
		'namespaces' => array(
			'Core' => ROOT_APP . 'apps/library/Core/',
			'Core\Acl' => ROOT_APP . 'apps/library/Core/Acl/',
			'Core\Controllers' => ROOT_APP . 'apps/library/Core/Controller/',
			'Core\Traits' => ROOT_APP . 'apps/library/Core/Traits/',
			'Core\Utils' => ROOT_APP . 'apps/library/Core/Utils/',
			'Core\Mail' => ROOT_APP . 'apps/library/Core/Mail/',
			'Core\Form\Element' => ROOT_APP . 'apps/library/Core/Form/Element/',
			'Objects' => ROOT_APP . 'apps/objects/',
			'Thirdparty\Geo' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
			'Thirdparty\Facebook' => ROOT_APP . 'apps/library/Thirdparty/Facebook/',
			'Thirdparty\FacebookGraph' => ROOT_APP . 'apps/library/Thirdparty/FacebookGraph/',
			'Categoryzator' => ROOT_APP . 'apps/library/Thirdparty/Categoryzator/',
			'Sharding' => ROOT_APP . 'apps/library/Thirdparty/Sharding/',
            'Thirdparty\MobileDetect' => ROOT_APP . 'apps/library/Thirdparty/MobileDetect/',
		),
		'mailer' => array(
			'path' => ROOT_LIB . 'Mail/Swift/swift_required.php',
            'config' => array(
                'transport' => 1, // 1 = SMTP, 2 = SendMail, 3 = Mail
                'smtp' => array(
                    'host' => 'smtp.mandrillapp.com',
                    'security' => 'tls',
                    'port' => 587,
                    'user' => 'slava@tiestegroup.com',
                    'pass' => '4NEhZpEAVCbZBjD5C-49-A'
                )
            )
		),
        'GeoIp2' => [
            'userId' => 86277,
            'licenseKey' => 'gAsqWYLrLpdh'
        ],
		'geo' => array(
			'path' => ROOT_APP . 'apps/library/Thirdparty/SxGeo/',
		),
        'uploadDir' => ROOT_APP . 'public/upload/',
		'uploadImgDir' => ROOT_APP . 'public/upload/img/',
		'relUploadImgDir' => '/upload/img/',
        'tempDir' => ROOT_APP . 'var/tmp/',
        'defaultLogo' => '/img/logo200.png',
		'limitFetchEvents' => 50,
		'pingFbPeriod' => 3600	// seconds
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
				'Frontend\Component' => ROOT_FRONT . 'components/',
			),
			'bootstrapNs' => 'Frontend\Bootstrap',
			'bootstrapPath' => ROOT_FRONT . 'Bootstrap.php',
			'defaultNameSpace' => 'Frontend',
			'formNamespace' => 'Frontend\Form',
		)
	)
);
