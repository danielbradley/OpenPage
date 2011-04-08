<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class DefaultUserSignupController extends Controller
{
	function DefaultUserSignupController()
	{}
	
	function perform( $session, $request, $debug )
	{
		$debug->println( "<!-- DefaultUserSignupController::perform() start -->" );
		$debug->indent();
		{
			$msg = "<!-- performing: " . $request["action"] . " -->";
			$debug->println( $msg );
			
			switch ( $request["action"] )
			{
			case "create_user":
				$ret = $this->createUser( $session, $request, $debug );
				break;
			}
		}
		$debug->outdent();
		$debug->println( "<!-- DefaultUserSignupController::perform() end -->" );

		return $ret;
	}

	function createUser( $session, $request, $debug )
	{
		$success = False;
	
		$debug->println( "<!-- DefaultUserSignupController::perform() start -->" );
		$debug->indent();
		{
			$username = $request['email1'];
			$password = $request['pass1'];
		
			if ( ($success = DBi_callFunction( DATABASE, "user_create( '$username', '$password', 'DEFAULT', '', '' )", $debug )) )
			{
				$debug->println( "<!-- User created -->" );
			} else {
				$debug->println( "<!-- User was not created -->" );
			}
		}
		$debug->outdent();
		$debug->println( "<!-- DefaultUserSignupController::perform() end -->" );
		
		return $success;
	}
}

?>