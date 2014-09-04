<?php

$config = 
[
	'environment' => 'phalcon',
	'connections'  => [
		'dbMaster' => [
			'adapter' => 'mysql',		
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'test',
			'writable' => true
		],
		/*'dbSlave' => [
			'adapter' => 'mysql',		
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'ew',
			'writable' => false
		],*/
	],
	'masterConnection' => 'dbMaster',
	'defaultConnection' => 'dbMaster',
	'shardMapPrefix' => 'shard_mapper_',
	'shardIdSeparator' => '_',
	'shardModels' => [
		'Event' => [
			'namespace' => '\Frontend\Models\\',
			'criteria' => 'location_id',
			'primary' => 'id',
			'baseTable' => 'event',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbMaster' => [
					'baseTablePrefix' => 'event_',
					'tablesMax' => 10
				]
			]
		],
		'Venue' => [
			'namespace' => '\Frontend\Models\\',
			'criteria' => 'location_id',
			'primary' => 'id',
			'baseTable' => 'venue',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbMaster' => [
					'baseTablePrefix' => 'venue_',
					'tablesMax' => 10
				]
			]
		],
	] 
];

return $config;