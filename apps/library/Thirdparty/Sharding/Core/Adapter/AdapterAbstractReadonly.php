<?php 

namespace Sharding\Core\Adapter;

use Sharding\Core\Adapter\AdapterAbstract;

abstract class AdapterAbstractReadonly extends AdapterAbstract 
{
	public final save()
	{
		return;
	}
	
	public final delete()
	{
		return;
	}
	
	public final update()
	{
		return;
	}
	
	public final createTable($tblName, $data)
	{
		return;
	}
}