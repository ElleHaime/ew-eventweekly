<?php

$config = 
[
	'environment' => 'phalcon',
	'connections'  => [
		'dbSlave' => [
			'adapter' => 'mysql',		
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'ew',
			'writable' => true
		]
	],
	'shardModels' => [
		'Event' => [
			'criteria' => 'location_id',
			'primary' => 'id',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbSlave' => [
					'baseTableName' => 'event',
					'tablesMin' => 1,
					'tablesMax' => 10
				]
			]
		],
		'Member' => [
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
		],
		'Venue' => [
			'criteria' => 'location_id',
			'primary' => 'id',
			'shardType' => 'loadbalance',
			'shards' => [
				'dbSlave' => [
					'baseTableName' => 'venue',
					'tablesMin' => 1,
					'tablesMax' => 10
				]
			]
		]
	] 
];

return $config;