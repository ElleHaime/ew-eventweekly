<?php 

namespace Sharding\Core\Adapter;

abstract class AdapterAbstract
{
	abstract function getConnection();
}