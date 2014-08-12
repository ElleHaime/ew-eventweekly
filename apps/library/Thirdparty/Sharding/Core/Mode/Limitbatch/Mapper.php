<?php 

namespace Sharding\Core\Mode\Limitbatch;

use Sharding\Core\Mode\ModeAbstract;

class Mapper extends ModeAbstract
{
	public $id;
	public $criteria_min;
	public $criteria_max;
	public $database;
	public $tablename;
}