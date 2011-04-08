<?php
//	Copyright (c) 2009, 2010 Daniel Robert Bradley. All rights reserved.
//	This software is distributed under the terms of the GNU Lesser General Public License version 2.1
?>
<?php

class InstallController extends Controller
{
	function __construct()
	{}
	
	function perform( $session, $request, $debug )
	{
		$ret = null;
		
		$debug->println( "<!-- InstallController::perform() start -->" );
		$debug->indent();
		{
			if ( array_key_exists( "action", $request ) )
			{
				$msg = "<!-- performing: " . $request["action"] . " -->";
				$debug->println( $msg );

				switch ( $request["action"] )
				{
				case "initialise_db":
					$ret = $this->install( $request, $debug );
					break;
				}
			}
		}
		$debug->outdent();
		$debug->println( "<!-- InstallController::perform() end -->" );

		return $ret;
	}

	function install( $request, $debug )
	{
		$status = False;

		$debug->println( "<!-- InstallController::install() start -->" );
		$debug->indent();

		switch ( $request["submit"] )
		{
		case "Install":
			$status = $this->installTables( $request, $debug );
			break;
		}

		$debug->outdent();
		$debug->println( "<!-- InstallController::install() end -->" );

		return $status;
	}

	function endsWith( $str, $sub )
	{
		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
	}
	
	function installTables( $request, $debug )
	{
		$open_dir = OPENPAGE_INC . "/_SQL";
		$user_dir = INCLUDEDIR   . "/_SQL";
		$sp_dir   = INCLUDEDIR   . "/_SQL_SP";

		$status = True;
		$status &= $this->createDatabase( $request, $debug );
		$status &= $this->grantExecuteToPublic( $request, $debug );
		$status &= $this->installTablesIn( $request, $debug, $open_dir );
		$status &= $this->installTablesIn( $request, $debug, $user_dir );
		$status &= $this->installTablesIn( $request, $debug, $sp_dir );
		
		return $status;
	}

	function createDatabase( $request, $debug )
	{
		$status = False;

		$debug->println( "<!-- InstallController::createDatabase start -->" );
		$debug->indent();
		{
			$dbadmin    = $request["dbadmin"];
			$dbpassword = $request["dbpassword"];

			$opusername = OPENPAGE_USERNAME;
			$oppassword = OPENPAGE_PASSWORD;
			$ophostname = OPENPAGE_HOSTNAME;

			$database_name = DBPREFIX . DATABASE;
			
			$db = new DBi( $dbadmin, HOSTNAME, $dbpassword, False );
			if ( $db->connect( $debug ) )
			{
				$sql = "CREATE DATABASE $database_name";
				if ( $db->change( null, $sql, $debug ) )
				{
					$status = True;
					$debug->println( "<!-- Created Database: $database_name -->" );
				} else {
					$debug->println( "<!-- Could not create database! -->" );
				}
			}
			else
			{
				$debug->println( "<!-- Invalid credentials for connection to db -->" );
			}
		}
		$debug->outdent();
		$debug->println( "<!-- InstallController::createDatabase end : $status -->" );
		
		return $status;
	}

	function grantExecuteToPublic( $request, $debug )
	{
		$status = True;

		$debug->println( "<!-- InstallController::grantExecuteToPublic start -->" );
		$debug->indent();
		{
			$dbadmin    = $request["dbadmin"];
			$dbpassword = $request["dbpassword"];

			$opusername = OPENPAGE_USERNAME;
			$oppassword = OPENPAGE_PASSWORD;
			$ophostname = OPENPAGE_HOSTNAME;

			$database_name = DBPREFIX . DATABASE;
			
			if ( ($dir = opendir( $sql_dir )) )
			{
				$db = new DBi( $dbadmin, HOSTNAME, $dbpassword, False );
				if ( $db->connect( $debug ) )
				{
					$sql = "GRANT EXECUTE ON $database_name.* TO '$opusername'@'$ophostname' IDENTIFIED BY '$oppassword'";
					$status = $db->change( DATABASE, $sql, $debug );
					if ( $status )
					{
						$debug->println( "<!-- Added auth -->" );
					}
				}
				else
				{
					$debug->println( "<!-- Invalid credentials for connection to db -->" );
				}
			}
		}
		$debug->outdent();
		$debug->println( "<!-- InstallController::grantExecuteToPublic end : $status -->" );
		
		return $status;
	}
	
	function installTablesIn( $request, $debug, $sql_dir )
	{
		$status = True;

		$debug->println( "<!-- InstallController::installTables start ($sql_dir) -->" );
		$debug->indent();
		{
			$dbadmin    = $request["dbadmin"];
			$dbpassword = $request["dbpassword"];

			$opusername = OPENPAGE_USERNAME;
			$oppassword = OPENPAGE_PASSWORD;
			$ophostname = OPENPAGE_HOSTNAME;

			$database_name = DBPREFIX . DATABASE;
			
			if ( ($dir = opendir( $sql_dir )) )
			{
				$db = new DBi( $dbadmin, HOSTNAME, $dbpassword, False );
				if ( $db->connect( $debug ) )
				{
					/*
					   Below are two almost identical loops, the first processes .sql files starting with '_'.
					   The second processes .sql files that don't.
					 */

					while ( false !== ($file = readdir( $dir )) )
					{
						if ( ("." != $file[0]) && ("_blank.sql" != "$file") && $this->endsWith( $file, ".sql" ) )
						{
							if ( "_" == $file[0] )
							{
								$debug->println( "<!-- Trying to load: $file -->" );
								$sql = SQL_loadfile( $sql_dir . "/" . $file );
								if ( False !== $sql )
								{
									if ( $db->multichange( DATABASE, $sql, $debug ) )
									{
										$debug->println( "<!-- Added $file -->");
									}
									else
									{
										$status = False;
									}
								}
							}
						}
					}

					rewinddir( $dir );

					while ( false !== ($file = readdir( $dir )) )
					{
						if ( ("." != $file[0]) && ("_blank.sql" != "$file") && $this->endsWith( $file, ".sql" ) )
						{
							if ( "_" != $file[0] )
							{
								$debug->println( "<!-- Trying to load: $file -->" );
								$sql = SQL_loadfile( $sql_dir . "/" . $file );
								if ( $db->multichange( DATABASE, $sql, $debug ) )
								{
									$debug->println( "<!-- Added $file -->");
								}
								else
								{
									$status = False;
								}
							}
						}
					}

				}
				else
				{
					$debug->println( "<!-- Invalid credentials for connection to db -->" );
				}
				closedir( $dir );
			}
		}
		$debug->outdent();
		$debug->println( "<!-- InstallController::installTables end : $status -->" );
		
		return $status;
	}
}
?>