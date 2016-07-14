<?php 

$cfg_rabbit_recipient = array(
	'host' => 'localhost',
	'vhost' => '/',
	'port' => '5672',
	'login' => 'guest',
	'password' => 'guest',
	'exchange' => 'amq.fanout',
	'routing_key' => 'harvester',
);

$cfg_rabbit_distributor = array(
	'host' => 'localhost',
	'vhost' => '/',
	'port' => '5672',
	'login' => 'guest',
	'password' => 'guest',
	'exchange' => 'amq.fanout',
	'routing_key' => 'distributor',
);

$cfg_memcache = array(
	'host' => '127.0.0.1',
	'port' => '11211'
);

$cfg_elastic = array(
    'index' => 'eventweeklytest',
    'connections' => [
        [
            'host' => 'localhost',
            'port' => 9200
        ]
    ]
);
