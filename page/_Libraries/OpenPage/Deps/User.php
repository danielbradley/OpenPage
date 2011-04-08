<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class User
{
	public $request;
	
	public $email;
	public $password;

	public $salt;
	public $ehash;
	public $phash;

	function User( $request )
	{
		$this->request = $request;

		$this->email    = $this->request["email"];
		$this->password = $this->request["password"];
	}

	function isValid( $debug )
	{
		$debug->println( "<!-- User::isValid() start -->" );
		$debug->indent();
		{
			$result = ( ("" != "$this->email") && ("" != "$this->password") );
			
			if ( ! $result ) $debug->println( "<!-- User is not valid -->" );
		}
		$debug->outdent();
		$debug->println( "<!-- User::isValid() end -->" );

		return $result;
	}

	function exists( $debug )
	{
		$status = False;
	
		$debug->println( "<!-- User::exists() start -->" );
		$debug->indent();
	
		$email = $this->email;
	
		$db = DBi_create();
		if ( $db->connect( $debug ) )
		{
			//$sql = "SELECT * FROM users WHERE email='$email'";
			$sql = "CALL user_exists( '$email' )";
			
			$tuples = $db->query( "oysta", $sql, $debug );
			if ( count( $tuples ) > 0 )
			{
				$status = True;
				$debug->println( "<!-- Exists -->" );
			}
			else
			{
				$debug->println( "<!-- Does not exist -->" );
			}
		}
	
		$debug->outdent();
		$debug->println( "<!-- User::exists() end -->" );

		return $status;
	}

	function create( $debug )
	{
		$status = False;
	
		$debug->println( "<!-- User::create() start -->" );
		$debug->indent();

		$email    = $this->email;
		$password = $this->password;

		DBi_callFunction( "user_create( '$email', '$password', 'DEFAULT' )" );
		
		$debug->outdent();
		$debug->println( "<!-- User::create() end -->" );

		return $status;
	}
}

?>