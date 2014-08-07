<?php

$config = 
[
	'environment' => 'phalcon',
	'connections'  => [
		'db1' => [
			'adapter' => 'mysql',		
			'host' => '127.0.0.1',
			'port' => '3307',
			'user' => 'root',
			'password' => 'root',
			'database' => 'ew'
		],
		'db2' => [
			'adapter' => 'mysql',
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'database' => 'ew'
		],
	],
	'shardModels' => [
		'event' => [
			'criteria' => 'location_id',
			'primary' => 'id',
			'shards' => [
				'db1' => [
					'baseTableName' => 'event',
					'tablesMin' => 1,
					'tablesMax' => 10,
					'shardType' => 'loadBalancer'
				],
				'db2' => [
					'baseTableName' => 'event',
					'tablesMin' => 1,
					'tablesMax' => 10,
					'shardType' => 'loadBalancer'
				]
			]
		],
		'member' => [
			'criteria' => 'id',
			'primary' => 'id',
			'shards' => [
				'db1' => [
					'baseTableName' => 'member',
					'tablesMin' => 1,
					'tablesMax' => 10,
					'shardType' => 'limitBatch'
				]
			]
		],
		'venue' => [
			'criteria' => 'id',
			'primary' => 'id',
			'shards' => [
				'db1' => [
					'baseTableName' => 'venue',
					'tablesMin' => 1,
					'tablesMax' => 2,
					'shardType' => 'oddEven'
				]
			]
		]
	] 
];

return $config;