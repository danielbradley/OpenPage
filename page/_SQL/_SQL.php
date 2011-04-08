<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

function SQL_loadfile( $filename )
{
	$fp = fopen( $filename, 'r' );
	$size = filesize( $filename );
	
	if ( $fp && (0 < $size) )
	{
		$sql = fread( $fp, $size );
		fclose( $fp );
		return $sql;
	} else {
		return False;
	}
}

?>