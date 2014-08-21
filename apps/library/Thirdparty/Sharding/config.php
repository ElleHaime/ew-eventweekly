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
			'database' => 'ew',
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
	'shardMapPrefix' => 'shard_mapper_',
	'shardModels' => [
		'Event' => [
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
		/*'Member' => [
			'criteria' => 'id',
			'primary' => 'id',
			'shardType' => 'limitbatch',
			'shards' => [
				'dbSlave' => [
					'baseTableName' => 'member',
					'tablesMax' => 10
				]
			]
		]*/
	] 
];

return $config;