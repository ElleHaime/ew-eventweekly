<?php 

namespace Sharding\Core\Mode;

interface StrategyInterface
{
	public function getShard($entity, $model, $args);
}