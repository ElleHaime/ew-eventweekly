<?php 

namespace Sharding\Core\Mode\Loadbalance;

use Sharding\Core\Mode\ModeAbstract;

class Mapper extends ModeAbstract
{
	public $id;
	public $criteria;
	public $database;
	public $tablename;
}