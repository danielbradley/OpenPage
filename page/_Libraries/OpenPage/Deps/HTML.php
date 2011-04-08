<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

function HTML_form_anchor( $name, $class, $href, $text, $hidden_inputs, $out )
{
//	echo "<form style='margin: 0px; padding: 0px;' class='form_anchor' name='$name' method='post' action='$href'>\n";
//	
//	if ( $hidden_inputs )
//	{
//		foreach ( $hidden_inputs as $key => $value )
//		{
//			echo "<input type='hidden' name='$key' value='$value'>\n";
//		}
//	}
//		
//	echo "<input type='submit' name='submit' value='$text' >\n";
//	echo "</form>\n";

	$out->println( "<form style='margin: 0px; padding: 0px;' class='form_anchor' name='$name' method='post' action='$href'>" );
	$out->indent();
	{
		if ( $hidden_inputs )
		{
			foreach ( $hidden_inputs as $key => $value )
			{
				$out->println( "<input type='hidden' name='$key' value='$value'>" );
			}
		}
		$out->println( "<a class='$class' href='#' onclick='document.$name.submit(); return false;'>$text</a>" );
	}
	$out->outdent();
	$out->println( "</form>" );
}

function HTML_form_session_anchor( $session, $hidden_inputs, $name, $class, $href, $text, $out )
{
//	echo "<form style='margin: 0px; padding: 0px;' class='form_anchor' name='$name' method='post' action='$href'>\n";
//	
//	if ( $hidden_inputs )
//	{
//		foreach ( $hidden_inputs as $key => $value )
//		{
//			echo "<input type='hidden' name='$key' value='$value'>\n";
//		}
//	}
//		
//	echo "<input type='submit' name='submit' value='$text' >\n";
//	echo "</form>\n";

	$out->println( "<form style='margin: 0px; padding: 0px;' class='form_anchor' name='$name' method='post' action='$href'>" );
	$out->indent();
	{
		$session->write( $out, $hidden_inputs );
		$out->println( "<a class='$class' href='#' onclick='document.$name.submit(); return false;'>$text</a>" );
	}
	$out->outdent();
	$out->println( "</form>" );
}

class Element
{
	function render( $out )
	{}
}

class Entity extends Element
{
	public $s;

	function Entity( $str )
	{
		$this->s = $str;
	}
	
	function render( $out )
	{
		$out->println( $this->s );
	}
}

class Sequence extends Element
{
	public $elements;

	function Sequence()
	{
		$this->elements = array();
	}

	function add( $element )
	{
		$this->elements[] = $element;
		return $this;
	}

	function render( $out )
	{
		foreach ( $this->elements as $element )
		{
			$element->render( $out );
		}
	}
}

function HTML_isChecked( $id1, $id2 )
{
	if ( $id1 == $id2 )
	{
		echo "checked";
	}
}

class Radio extends Element
{
	private $attributes;
	private $selected;
	private $options;

	function Radio( $attributes, $selected, $options )
	{
		$this->attributes = $attributes;
		$this->selected   = $selected;
		$this->options    = $options;
	}
	
	function render( $out )
	{
		$out->println( "" );
		foreach ( $this->options as $value => $text )
		{
			if ( "" == $text ) $text = $value;
		
			if ( "$value" == "$this->selected" )
			{
				$out->println( "<input $this->attributes class='checkbox' type='radio' value='$value' checked='checked'> $text<br>" );
			} else {
				$out->println( "<input $this->attributes class='checkbox' type='radio' value='$value' > $text<br>" );
			}
		}
	}
}

class Select extends Element
{
	private $attributes;
	private $selected;
	private $options;

	function Select( $attributes, $selected, $options )
	{
		$this->attributes = $attributes;
		$this->selected   = $selected;
		$this->options    = $options;
	}
	
	function render( $out )
	{
		$out->println( "" );
		$out->println( "<select $this->attributes>" );
		$out->indent();
		{
			foreach ( $this->options as $value => $text )
			{
				if ( "" == $text ) $text = $value;
			
				if ( "$value" == "$this->selected" )
				{
					$out->println( "<option value='$value' selected>$text</option>" );
				} else {
					$out->println( "<option value='$value'>$text</option>" );
				}
			}
		}
		$out->outdent();
		$out->println( "</select>" );
	}

	function renderEncoded( $out )
	{
		$out->println( "" );
		$out->println( "&lt;select $this->attributes&gt;" );
		$out->indent();
		{
			foreach ( $this->options as $value => $text )
			{
				if ( "" == $text ) $text = $value;
			
				if ( "$value" == "$this->selected" )
				{
					$out->println( "&lt;option value='$value' selected&gt;$text&lt;/option&gt;" );
				} else {
					$out->println( "&lt;option value='$value'&gt;$text&lt;/option&gt;" );
				}
			}
		}
		$out->outdent();
		$out->println( "&lt;/select&gt;" );
	}
}

class GroupedSelect extends Element
{
	private $attributes;
	private $selected;
	private $option_groups;

	function GroupedSelect( $attributes, $selected, $option_groups )
	{
		$this->attributes    = $attributes;
		$this->selected      = $selected;
		$this->option_groups = $option_groups;
	}
	
	function render( $out )
	{
		$out->println( "" );
		$out->println( "<select $this->attributes>" );
		$out->indent();
		{
			foreach ( $this->option_groups as $category => $group )
			{
				$out->println( "<optgroup label='$category'>" );
				$out->indent();
				{
					foreach ( $group as $value => $text )
					{
						if ( "" == $text ) $text = $value;
						
						if ( "$value" == "$this->selected" )
						{
							$out->println( "<option value='$value' selected>$text</option>" );
						} else {
							$out->println( "<option value='$value'>$text</option>" );
						}
					}
				}
				$out->outdent();
				$out->println( "</optgroup>" );
			}
		}
		$out->outdent();
		$out->println( "</select>" );
	}
}

class TD extends Element
{
	public $element;
	public $attributes;
	
	function TD( $attributes, $e )
	{
		$this->attributes = "";
		if ( null != $attributes ) $this->attributes = " " . $attributes;

		if ( is_string( $e ) )
		{
			$this->element = new Entity( $e );
		}
		else
		{
			$this->element = $e;
		}
	}
	
	function setAttributes( $s )
	{
		return $this;
	}
	
	function render( $out )
	{
		$out->println( "<td$this->attributes>" );
		$out->indent();
		{
			$this->element->render( $out );
		}
		$out->outdent();
		$out->println( "</td>" );
	}
}

class TH extends TD
{
	function render( $out )
	{
		$out->println( "<th$this->attributes>" );
		$out->indent();
		{
			$this->element->render( $out );
		}
		$out->outdent();
		$out->println( "</th>" );
	}
}

class TR extends Element
{
	public $attributes;
	public $sequence;

	function TR( $attributes, $sequence )
	{
		$this->attributes = "";
		if ( null != $attributes ) $this->attributes = " " . $attributes;
	
		$this->sequence = $sequence;
		if ( null == $this->sequence ) $this->sequence = new Sequence();
	}

	function add( $td )
	{
		$this->sequence->add( $td );
	}

	function render( $out )
	{
		$out->println( "<tr" . $this->attributes . ">" );
		$out->indent();
		{
			$this->sequence->render( $out );
		}
		$out->outdent();
		$out->println( "</tr>" );
	}
}

class Table
{
	public $attributes;

	public $head_rows;
	public $body_rows;
	
	function Table( $attributes )
	{
		$this->attributes = "";
		if ( null != $attributes ) $this->attributes = " " . $attributes;

		$this->head_rows = array();
		$this->body_rows = array();
	}
	
	function addToHead( $row )
	{
		$this->head_rows[] = $row;
	}

	function addToBody( $row )
	{
		$this->body_rows[] = $row;
	}
	
	function render( $out )
	{
		$out->println( "<table" . $this->attributes . ">" );
		$out->println( "<thead>" );
		$out->indent();
		{
			foreach ( $this->head_rows as $tr )
			{
				$tr->render( $out );
			}
		}
		$out->outdent();
		$out->println( "</thead>" );
		$out->println( "<tbody>" );
		{
			foreach ( $this->body_rows as $tr )
			{
				$tr->render( $out );
			}
		}
		$out->println( "</tbody>" );
		$out->println( "</table>" );
	}
}

class Video extends Element
{
	public $data;
	private $type;
	private $src;
	private $width;
	private $height;
	private $autoplay;
	private $controller;
	private $loop;

	function Video( $data, $attributes, $type, $src, $width, $height, $autoplay, $controller, $loop )
	{
		$this->data       = $data;
		$this->attributes = $attributes;
		$this->type       = $type;
		$this->src        = $src;
		$this->width      = $width;
		$this->height     = $height;
		$this->autoplay   = $autoplay;
		$this->controller = $controller;
		$this->loop       = $loop;
	}

	function render( $out )
	{
		switch ( $this->type )
		{
		case "Quicktime":
			$this->renderQuicktimeJS( $out );
			break;
		}
	}

	function renderQuicktimeJS( $out )
	{
		$out->println( "<!---------------------------------------------------------------->" );
		$out->println( "<!-- Moved Video Object into Java Script following advice from: -->" );
		$out->println( "<!-- http://www.apple.com/quicktime/tutorials/embed.html        -->" );
		$out->println( "<!---------------------------------------------------------------->" );
	
		$out->println( "<script language='JavaScript' type='text/javascript'>" );
		$out->indent();
		{
			$out->println( "document.write( \"<object $this->attributes classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='$this->width' height='$this->height' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>\" );" );
			$out->indent();
			{
				$out->println( "document.write( \"<param name='src' value='$this->src'>\" );" );
				$out->println( "document.write( \"<param name='autoplay'   value='$this->autoplay'>\" );" );
				$out->println( "document.write( \"<param name='controller' value='$this->controller'>\" );" );
				$out->println( "document.write( \"<param name='loop'       value='$this->loop'>\" );" );
				$out->println( "document.write( \"<embed src='$this->src' width='$this->width' height='$this->height' autoplay='$this->autoplay' controller='$this->controller' loop='$this->loop' pluginspage='http://www.apple.com/quicktime/download/'>\" );" );
				//$out->println( "document.write( \"</embed>\" );" );
			}
			$out->outdent();
			$out->println( "document.write( \"</object>\" );" );
		}
		$out->outdent();
		$out->println( "</script>" );
	}
	
	function renderQuicktimeJS1( $out )
	{
		$out->println( "<script language='JavaScript' type='text/javascript'>" );
		$out->indent();
		{
			$out->println( "document.write( \"<object $this->attributes classid=\'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\' width=\'$this->width\' height=\'$this->height\' codebase=\'http://www.apple.com/qtactivex/qtplugin.cab\'>\" );" );
			$out->indent();
			{
				$out->println( "document.write( \"<param name=\'src\' value=\'$this->src\'>\" );" );
				$out->println( "document.write( \"<param name=\'autoplay\'   value=\'$this->autoplay\'>\" );" );
				$out->println( "document.write( \"<param name=\'controller\' value=\'$this->controller\'>\" );" );
				$out->println( "document.write( \"<param name=\'loop\'       value=\'$this->loop\'>\" );" );
				$out->println( "document.write( \"<embed src='$this->src\' width=\'$this->width\' height=\'$this->height\' autoplay=\'$this->autoplay\' controller=\'$this->controller\' loop=\'$this->loop\' pluginspage=\'http://www.apple.com/quicktime/download/\'>\" );" );
				$out->println( "document.write( \"</embed>\" );" );
			}
			$out->outdent();
			$out->println( "document.write( \"</object>\" );" );
		}
		$out->outdent();
		$out->println( "</script>" );
	}

	function renderQuicktime( $out )
	{
		$out->println( "<object $this->attributes classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='$this->width' height='$this->height' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>" );
		$out->indent();
		{
			$out->println( "<param name='src' value='$this->src'>" );
			$out->println( "<param name='autoplay'   value='$this->autoplay'>" );
			$out->println( "<param name='controller' value='$this->controller'>" );
			$out->println( "<param name='loop'       value='$this->loop'>" );
			$out->println( "<embed src='$this->src' width='$this->width' height='$this->height' autoplay='$this->autoplay' controller='$this->controller' loop='$this->loop' pluginspage='http://www.apple.com/quicktime/download/'>" );
			$out->println( "</embed>" );
		}
		$out->outdent();
		$out->println( "</object>" );
	}
}

?>
