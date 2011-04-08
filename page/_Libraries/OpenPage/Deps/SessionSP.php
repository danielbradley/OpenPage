<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class SessionSP
{
	var $use_cookie;

	var $request;
	var $hostname;
	var $database;
	var $username;
	var $password;

	var $sessionid;

	//	Algorithm: new Session( $request, $hostname, $database, $username, $password )
	//		Input:
	//			$request, the request dictionary containing form data.
	//			$hostname, the hostname of the server running database, e.g. localhost.
	//			$database, the name of the database containing the users table.
	//			$username, the username of a user that is able to create an entry in the users table.
	//			$password, the password of a user that is able to create an entry in the users table.
	//
	function SessionSP( $request, $hostname, $database, $username, $password, $lower )
	{
		$this->use_cookie = True;
	
		$this->request  = $request;
		$this->hostname = $hostname;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		
		// Check for form session id first.
		$this->sessionid = "";
		if ( array_key_exists( "id", $request ) )
		{
			$this->sessionid = $request["id"];
		}
		// Then for sid.
		else if ( array_key_exists( "sid", $request ) )
		{
			$this->sessionid = $request["sid"];
		}
		// Then check for cookie sid.
		else if ( array_key_exists( "sid", $_COOKIE ) )
		{
			$this->sessionid = $_COOKIE["sid"];
		}
		
		if ( $lower ) $this->username = strtolower( $username );
	}

	function establish( $debug )
	{
		if ( $this->username || $this->sessionid ) return $this->verify( $debug );
		else return False;
	}

	function verify( $debug )
	{
		$verified = False;
	
		$debug->println( "<!-- SessionSP.verify() start -->" );
		$debug->indent();

		if ( array_key_exists( "logout", $this->request ) )
		{
			if ( "true" == $this->request['logout'] )
			{
				$this->terminate( $debug );
			}
		}
		else
		{
			if ( "" != $this->username )
			{
				$this->sessionid = $this->authenticate( $debug );
				switch ( $this->sessionid )
				{
				case "INVALID_USER":
				case "INVALID_PASSWORD":
					$verified = $this->sessionid;
					break;
				default:
					$verified = "AUTHENTICATED";
					break;
				}
			}
			else if ( "" != $this->sessionid )
			{
				$verified = $this->verifySession( $debug );
			}

			if ( $verified )
			{
				$this->writeAuthenticationCookie();
				$debug->println( "<!-- writeAuthenticationCookie() done -->" );
			}
		}
		$debug->outdent();
		$debug->println( "<!-- SessionSP.verify() end -->" );

		return $verified;
	}

	function terminate( $debug )
	{
		$debug->println ("<!-- SessionSP.terminate() start -->" );
		$debug->indent();
		{
			$id = $this->sessionid;
			if ( DBi_callFunction( DATABASE, "session_terminate( '$id' )", $debug ) )
			{
				$debug->println( "<!-- Session Terminated -->" );
			} else {
				$debug->println( "<!-- Session Not Found -->" );
			}
			header( "Set-Cookie: sid=; expires=Thu, 1-Jan-2009 01:01:01 GMT; path=/" );
		}
		$debug->outdent();
		$debug->println ("<!-- SessionSP.terminate() end -->" );
	}

	function authenticate( $debug )
	{
		$id = null;
	
		$username = $this->username;
		$password = $this->password;
	
		$debug->println ("<!-- SessionSP.authenticate() start -->" );
		$debug->indent();
		{
			$id = DBi_callFunction( DATABASE, "session_authenticate( '$username', '$password' )", $debug );
			$debug->println( "<!-- authenticated: $id -->" );
		}
		$debug->outdent();
		$debug->println ("<!-- SessionSP.authenticate() end -->" );

		return $id;
	}

	function verifySession( $debug )
	{
		$verified = False;

		$debug->println ("<!-- Session.verifySession() start -->" );
		$debug->indent();
		{
			$id = $this->sessionid;
			if ( $id )
			{
				if ( DBi_callFunction( DATABASE, "session_verify( '$id' )", $debug ) )
				{
					$verified = "AUTHENTICATED";
				} else {
					$verified = "INVALID_SESSION";
				}
				$debug->println( "<!-- authenticated: $verified -->" );
			}
		}

		$debug->outdent();
		$debug->println ("<!-- Session.verifySession() end -->" );

		return $verified;
	}
	
	function writeAuthenticationCookie()
	{
		if ( $this->use_cookie )
		{
			$cookie = "Set-Cookie: sid=";
			$cookie = $cookie . $this->sessionid;
			$cookie = $cookie . "; path=/";
		
			header( $cookie );
		}
	}
	
	function write( $out, $hidden_inputs )
	{
		if ( ! $this->use_cookie )
		{
			$out->println( "<input type='hidden' name='id' value='$this->sessionid'>" );
			//$out->println( "<input type='hidden' name='email' value='$this->username'>" );
			//$out->println( "<input type='hidden' name='password' value='$this->password'>" );
		}
			
		if ( $hidden_inputs )
		{
			foreach ( $hidden_inputs as $key => $value )
			{
				$out->println( "<input type='hidden' name='$key' value='$value'>" );
			}
		}
	}
}

?>
