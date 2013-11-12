<?php

namespace Core;

class Utils
{
	public static function dump($params, $todie = false)
	{
		echo '<div style="background-color:#cecece;"><pre>'; 
		var_dump($params); 
		echo '</pre></div>'; 
		
		if (!$todie) {
			die();
		}
	}
}
