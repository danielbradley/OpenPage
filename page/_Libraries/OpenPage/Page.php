<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php
// 0.7.0


include( "Deps/DBi.php" );
include( "Deps/HTML.php" );
include( "Deps/Input.php" );
include( "Deps/MVC.php" );
include( "Deps/Printer.php" );
include( "Deps/SessionSP.php" );
include( "Deps/User.php" );

function array_get( $key, $array )
{
	return isset( $array ) && array_key_exists( $key, $array ) ? $array[$key] : "";
}

function force_array( $object )
{
	if ( isset( $object ) && is_array( $object ) )
	{
		return $object;
	} else {
		return array();
	}
}

function first( $tuples )
{
	if ( is_array( $tuples ) )
	{
		return array_key_exists( 0, $tuples ) ? $tuples[0] : null;
	}
}

class Page
{
	public $unchecked_request;
	public $request;
	public $pageId;
	public $authenticated;
	public $debug;
	public $out;
	public $hidden_inputs;

	public $session;

	function __construct( $request )
	{
		$this->out   = new Printer();
		$this->debug = ( DEBUG ) ? new Printer() : new NullPrinter();
		$this->debug->startBuffering(); // Writes buffer when body() is called so as to not interfer with headers.

		$this->debug->println( "<!-- Page() start -->" );
		$this->debug->indent();
		{
			$script_name = $_SERVER["SCRIPT_NAME"];
		
			$this->request  = FilterInput( $request, $this->debug );
			$this->pageId   = Page::generatePageId();
			$this->pagePath = Page::generatePagePath();
			$this->establishSession( $this->debug );
		}
		$this->debug->outdent();
		$this->debug->println( "<!-- Page() end -->" );
		$this->debug->println( "" );
	}

		function establishSession( $debug )
		{
			$request = $this->request;
		
			$username = (array_key_exists( "username", $request )) ? $request["username"] : "";
			$password = (array_key_exists( "password", $request )) ? $request["password"] : "";
		
			$this->session = new SessionSP( $request, HOSTNAME, "", $username, $password, True );
			$this->authenticated = $this->session->establish( $debug );
		}

	function getPageId()
	{
		return $this->pageId;
	}

	function getPagePath()
	{
		return $this->pagePath;
	}
	
	function logout( $debug )
	{
		$this->session->terminate( $debug );
		unset( $this->session );
		unset( $this->authenticated );
	}
	
	function render()
	{
		$this->redirect( $this->debug );
		$this->presync( $this->debug );
		$this->loadMenus( $this->debug );
		$this->htmlStart( $this->out );
		{
			$this->head( $this->out );
			$this->debug->writeBuffer();
			$this->sync( $this->debug );
			$this->body( $this->out );
		}
		$this->htmlEnd( $this->out );
	}

		function redirect( $debug )
		{}

		function presync( $debug )
		{}
		
		function loadMenus( $debug )
		{}

		function htmlStart( $out )
		{
			header("Content-Type: text/html; charset=utf-8");
			$this->doctype( $out );
			$out->println( "<html>" );
		}
		
			function doctype( $out )
			{
				$out->println( "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">" );
			}

		function head( $out )
		{
			$this->headStart( $out );
			$out->indent();
			{
				$this->title( $out );
				$this->meta( $out );
				$this->stylesheets( $out );
				$this->headContent( $out );
				$this->javascript( $out );
			}
			$out->outdent();
			$this->headEnd( $out );
		}

			function headStart( $out )
			{
				$out->println( "<head>" );
			}

			function title( $out )
			{
				$title = $this->getPageTitle();
			
				$out->println( "<title>$title</title>" );
			}

				function getPageTitle()
				{
					return "";
				}

			function meta( $out )
			{
				$out->println( "<meta http-equiv='Content-type' content='text/html;charset=UTF-8'>" );
				$out->println( "<meta name='viewport' content='width=device-width'>" );
			}

			function javascript( $out )
			{
				$out->println( "<script type='text/javascript' src='" . OPENPAGE . "/_Javascript/_common.js'></script>" );
			}

			function stylesheets( $out )
			{
				$out->println( "<link rel='stylesheet' type='text/css' href='" . OPENPAGE . "/_Styles/_OpenPage.css'>" );
			}

			function headContent( $out )
			{
			}

			function headEnd( $out )
			{
				$out->println( "</head>" );
			}

		function sync( $debug )
		{
			// Overridden by User Page.
			// Access database if required etc.
		}

		function body( $out )
		{
			$this->bodyStart( $out );
			$this->bodyContent( $out );
			$this->bodyEnd( $out );
		}

			function bodyStart( $out )
			{
				$out->println( "<body>" );
			}

			function bodyContent( $out )
			{
				$page_template = $this->getPageTemplate(); 
			
				$out->println( "<div class='page' id='$this->pageId'>" );
				$out->indent();
				{
					$out->println( "<div class='template' id='$page_template'>" );
					$out->indent();
					{
						$this->bodyHeader( $out );
						$this->bodyMenu( $out );
						$this->bodyBreadcrumbs( $out );
						$this->bodyMiddle( $out );
						$this->bodyFooter( $out );
					}
					$out->outdent();
					$out->println( "</div>" );
				}
				$out->outdent();
				$out->println( "</div>" );
				$this->finalJavascript( $out );
			}
			
				function getPageTemplate()
				{
					return "default";
				}

				function bodyHeader( $out )
				{}

				function bodyMenu( $out )
				{}

				function bodyBreadcrumbs( $out )
				{}

				function bodyMiddle( $out )
				{}

				function bodyFooter( $out )
				{}

			function bodyEnd( $out )
			{
				$out->println( "</body>" );
			}

		function htmlEnd( $out )
		{
			$out->println( "</html>" );
		}

	/*
	 *  Converts uri to form 'page-subpage-index', used to unique identify pages.
	 */
	static function generatePageId()
	{
		$uri = $_SERVER["SCRIPT_NAME"];
		$id  = (0 == stripos( $uri, "/page/" )) ? $uri : substr( $uri, stripos( $uri, "/page/" ) + 5 );
		$page_id = substr( $id, 1, count( $id ) - 5 );
		return str_replace( "/", "-", $page_id );
	}

	/*
	 *  Converts each element of uri to Title Case e.g. 'Page/Subpage'.
	 */
	static function generatePagePath()
	{
		$uri = $_SERVER["SCRIPT_NAME"];
		$path = "";
	
		$id = (0 == stripos( $uri, "/page/" )) ? $uri : substr( $uri, stripos( $uri, "/page/" ) + 5 );
		$id = substr( $id, 1, count( $id ) - 11 );
		
		$bits = explode( "/", $id );
		foreach ( $bits as $bit )
		{
			$path .= "/" . Page::toTitleCase( $bit );
		}
		
		return substr( $path, 1 );
	}
		
		static function toTitleCase( $string )
		{
			$ret = "";
		
			$bits = explode( "_", $string );
			foreach ( $bits as $bit )
			{
				if ( !empty( $bit ) ) $ret .= " " . strtoupper( $bit[0] ) . substr( $bit, 1 );
			}
			return substr( $ret, 1 );
		}
	
}

?>
