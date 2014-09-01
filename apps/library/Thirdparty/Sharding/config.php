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
			'criteria' => 'location_id',
			'primary' => 'id',
			'baseTable' => 'event',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbMaster' => [
					'baseTablePrefix' => 'event_',
					'tablesMax' => 11
				]
			]
		],
		/*'Member' => [
			'criteria' => 'id',
			'shard_interval' => 10,
			'primary' => 'id',
			'baseTable' => 'member',
			'shardType' => 'limitbatch',
			'shards' => [
				'dbMaster' => [
					'baseTableName' => 'member'
				]
			]
		]*/
	] 
];

return $config;