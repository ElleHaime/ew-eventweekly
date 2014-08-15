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
	'shardModels' => [
		'Event' => [
			'criteria' => 'location_id',
			'primary' => 'id',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbMaster' => [
					'baseTableName' => 'event',
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
					'tablesMin' => 1,
					'tablesMax' => 10
				]
			]
		]*/
	] 
];

return $config;