<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

include( "$IncludeDir/_Configuration/sample_configuration.php" );
include( OPENPAGE_INC . "/_Libraries/OpenPage/Page.php" );

class SitePage extends Page
{
	public $hidden_inputs;

	function __construct( $request )
	{
		parent::__construct( $request );
	}
	
	function getPageTitle()
	{
		return $this->title;
	}

	function stylesheets( $out )
	{
		parent::stylesheets( $out );
		$out->println( "<link rel='stylesheet' type='text/css' href='" . OPENPAGE . "/_Styles/_Page.css'>" );
		$out->println( "<link rel='stylesheet' type='text/css' href='" . OPENPAGE . "/_Styles/_Site.css'>" );
	}

	function bodyStart( $out )
	{
		$out->println( "<body>" );
	}

	function bodyHeader( $out )
	{
		$username = $this->session->username;
?>
	<div class='header'>
		<div class='header_content'>
			<div class='logo'>
				<a href='<?php echo PAGES; ?>/home/'><span class='textual_logo'>OpenPage</span></a>
			</div>
			<div class='headright'>
				<div id='blurb'>
					<p>the light-weight web application framework</p>
				</div>
			</div>
		</div>
	</div>
<?php
	}

	function bodyMenu( $out )
	{
?>
	<div class='topmenu'>
		<div class='topmenu_content'>
			<table id='left_menu'>
				<tr>
					<td><a class='enabled' id='home'         href='<?php echo PAGES ?>/home/'         >Home         </a></td>
					<td><a class='enabled' id='home'         href='<?php echo PAGES ?>/about_us/'     >About Us     </a></td>
					<td><a class='enabled' id='home'         href='<?php echo PAGES ?>/documentation/'>Documentation</a></td>
					<td><a class='enabled' id='home'         href='<?php echo PAGES ?>/downloads/'    >Downloads    </a></td>
					<td><a class='enabled' id='sign_up'      href='<?php echo PAGES ?>/signup/'       >Sign Up      </a></td>

		<?php if ( $this->authenticated ) { ?> 
					<td><a class='enabled' href='<?php echo PAGE ?>/user/'       >User Home</a></td>
		<?php } ?> 
				</tr>
			</table>
			<table id='right_menu'>
		<?php if ( $this->authenticated ) { ?> 
				<td>
					<a class='enabled' href='<?php echo PAGES ?>/logout/' style='text-align: right'>Logout</a>
					<?php echo $this->credentials["email"] ?> 
				</td>
		<?php } else { ?> 
				<td><a class='enabled' id='member_login' href='<?php echo PAGES ?>/logon/' >Log On</a></td>
		<?php } ?> 
			</table>
		</div>
	</div>
<?php
	}

	function bodyMiddle( $out )
	{
		$out->println( "<div class='middle'>" );
		$out->indent();
		{
			$out->println( "<div class='middle_content'>" );
			$out->indent();
			{
				$this->middleLeft( $out );
				$this->middleCenter( $out );
				$this->middleRight( $out );
			}
			$out->outdent();
			$out->println( "</div>" );
		}
		$out->outdent();
		$out->println( "</div>" );
		$out->println( "<div class='footstool'>" );
		$out->println( "</div>" );
		
	}

	function middleLeft( $out )
	{
	}

	function middleCenter( $out )
	{
		$this->debug->println( "<!-- OYSTA:middleCenter() should be overridden by derived class -->" );
		
		if ( $this->request['sid'] || $this->request['id'] )
		{
?>
			<p class='medium'>
			<span style='font-size: 15pt;'>Uh Oh!</span>
			Your session has expired, you will need to login again to continue.
			</p>
<?php
		}
		else
		{
?>
	<p class='medium'>
	<span style='font-size: 15pt;'>Uh Oh!</span>
	Either your <b>email</b> or <b>password</b> was incorrect.
	</p>
	<p class='medium'>
	In the future we will allow you to request an email that will enable you to change your password by clicking a link.
	</p>
<?php
		}
	}

	function middleRight( $out )
	{
		//$out->println( "<!-- OYSTA:middleRight() should be overridden by derived class -->" );
	}

	function bodyFooter( $out )
	{
		$out->println( "<div class='footer'>" );
		$out->indent();
		{
			$out->println( "<div class='footer_content'>" );
			$out->indent();
			{
				$out->println( "<table id='copyright'>" );
				$out->indent();
				{
					$out->println( "<tr>" );
					$out->indent();
					{
						$out->println( "<td>Copyright &copy; 2009, 2010 The Austral Imperial Trust</td>" );
					}
					$out->outdent();
					$out->println( "</tr>" );
				}
				$out->outdent();
				$out->println( "</table>" );

				$out->println( "<table id='footer_menu'>" );
				$out->indent();
				{
					$out->println( "<tr>" );
					$out->indent();
					{
						$out->println( "<td><a href='" . PAGES . "/sign_up'   >Sign Up</a> |</td>" );
						$out->println( "<td><a href='" . PAGES . "/contact_us'>Contact Us</a> |</td>" );
						$out->println( "<td><a href='" . PAGES . "/sitemap'   >Sitemap</a></td>" );
					}
					$out->outdent();
					$out->println( "</tr>" );
				}
				$out->outdent();
				$out->println( "</table>" );
			}
			$out->outdent();
			$out->println( "</div>" );
		}
		$out->outdent();
		$out->println( "</div>" );
	}
}

?>