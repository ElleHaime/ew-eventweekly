<?php

$config = 
[
	'environment' => 'phalcon',
	'connections'  => [
		'db1' => [
			'adapter' => 'mysql',		
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'ew',
			'writable' => true
		],
		'db2' => [
			'adapter' => 'mysql',
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'test',
			'writable' => true
		],
	],
	'shardModels' => [
		'Event' => [
			'criteria' => 'location_id',
			'primary' => 'id',
			'shardType' => 'loadbalance',
			'shards' => [
				'db1' => [
					'baseTableName' => 'event',
					'tablesMin' => 1,
					'tablesMax' => 10
				],
				'db2' => [
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
				'db1' => [
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
				'db1' => [
					'baseTableName' => 'venue',
					'tablesMin' => 1,
					'tablesMax' => 10
				],
				'db2' => [
					'baseTableName' => 'venue',
					'tablesMin' => 1,
					'tablesMax' => 10
				]
			]
		]
	] 
];

return $config;