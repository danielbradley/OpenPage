<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class Printer
{
	public $tabs;
	public $buffer;
	public $buffering;
	
	function __construct( $initial = 0 )
	{
		$this->tabs = $initial;
		$this->buffering = False;
	}
	
	function startBuffering()
	{
		$this->buffering = True;
		$this->buffer = "";
	}
	
	function writeout( $str )
	{
		if ( $this->buffering )
		{
			$this->buffer = $this->buffer . $str;
		}
		else
		{
			echo $str;
		}
	}
	
	function writeBuffer()
	{
		echo $this->buffer;
		$this->buffer = "";
		$this->buffering = False;
	}
	
	function printf( $string )
	{
		for ( $i=0; $i < $this->tabs; $i ++ )
		{
			$this->writeout( "\t" );
		}
		$this->writeout( $string );
	}

	function append( $string )
	{
		$this->writeout( $string );
	}
	
	function println( $string = "" )
	{
		$this->printf( $string );
		$this->writeout( "\n" );
	}
	
	function inprint( $string = "" )
	{
		$this->println( $string );
		$this->indent();
	}
	
	function outprint( $string = "" )
	{
		$this->outdent();
		$this->println( $string );
	}

	function indent( $nr = 1 )
	{
		if ( $nr )
		{
			$this->tabs += $nr;
		} else {
			$this->tabs++;
		}
	}
	
	function outdent( $nr = 1 )
	{
		if ( $nr )
		{
			$this->tabs -= $nr;
		} else {
			$this->tabs--;
		}
	}
}

class NullPrinter extends Printer
{
	function printf( $string )
	{
	}

	function append( $string )
	{
	}
	
	function println( $string = "" )
	{
	}

	function indent( $nr = 1 )
	{
	}
	
	function outdent( $nr = 1 )
	{
	}
}

?>
