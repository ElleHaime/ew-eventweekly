<?php

namespace Core;

class Model extends \Phalcon\Mvc\Model
{

	public function createOnChange($argument)
	{
		return false;
	}
}