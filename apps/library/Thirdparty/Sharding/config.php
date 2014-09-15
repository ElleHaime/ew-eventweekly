<?php

$config = 
[
	'environment' => 'phalcon',
	'connections'  => [
		'dbMaster' => [
			'adapter' => 'mysql',		
			'host' => '127.0.0.1',
			'port' => '3307',
			'user' => 'root',
			'password' => 'root',
			'database' => 'test',
			'writable' => true
		]
	],
	'masterConnection' => 'dbMaster',
	'defaultConnection' => 'dbMaster',
	'shardMapPrefix' => 'shard_mapper_',
	'shardIdSeparator' => '_',
	'shardModels' => [
		'Event' => [
			'namespace' => '\Frontend\Models',
			'criteria' => 'location_id',
			'primary' => 'id',
			'baseTable' => 'event',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbMaster' => [
					'baseTablePrefix' => 'event_',
					'tablesMax' => 3
				]
			],
			'relations' => [
				'EventSite' => [
					'namespace' => '\Frontend\Models',
					'baseTable' => 'event_site',
					'baseTablePrefix' => 'event_site_',
				],
				'EventImage' => [
					'namespace' => '\Frontend\Models',
					'baseTable' => 'event_image',
					'baseTablePrefix' => 'event_image_'
				],
				'EventTag' => [
					'namespace' => '\Frontend\Models',
					'baseTable' => 'event_tag',
					'baseTablePrefix' => 'event_tag_',
				],
				'EventCategory' => [
					'namespace' => '\Frontend\Models',
					'baseTable' => 'event_category',
					'baseTablePrefix' => 'event_category_',
				]
			],
			'files' => [
				'images' => [
					'dependencyField' => 'id',
					'path' => '/var/www/EventWeekly/public/upload/img/event'
				]
			]
		]
	] 
];

return $config;