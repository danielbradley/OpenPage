<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

function FilterInput( $request, $debug )
{
	$filtered = array();

	$debug->println( "<!-- FilterInput() start -->" );
	$debug->indent();
	{
		
		$debug->println( "<!-- REQUEST -->" );
		$debug->indent();
		{
			foreach ( $request as $key => $value )
			{
				if ( "WG" != substr( $key, 0, 2 ) )
				{
					$val = addslashes( htmlentities( $value, ENT_QUOTES ) );
				}
				$debug->println( "<!-- $key | $val -->" );
				$filtered[$key] = $val;

			}
		}
		$debug->outdent();

		$debug->println( "<!-- COOKIE -->" );
		$debug->indent();
		{
			foreach ( $_COOKIE as $key => $value )
			{
				if ( ! array_key_exists( $key, $filtered ) )
				{
					$filtered[$key] = $value;
					$debug->println( "<!-- \"$key\" | \"$value\" -->" );
				}
			}
		}
		$debug->outdent();
	}
	$debug->outdent();
	$debug->println( "<!-- FilterInput() end -->" );

	return $filtered;
}

?>