<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class Model
{
	function update()
	{}
}

class View
{
	function render( $out )
	{}
}

class Controller
{
	function perform( $session, $request, $debug )
	{}
	
	function first( $array )
	{
		if ( isset( $array ) && is_array( $array ) )
		{
			if ( ! empty( $array ) )
			{
				return $array[0];
			}
		}
	}
}

?>
