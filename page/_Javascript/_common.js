function goBack()
{
	/* Does not work properly on all browsers.*/
	history.go(-2);
}

function detectAndIgnoreBackspace()
{
	/* http://www.webmasterworld.com/forum91/4699.htm */

	alert( "Bonza" );	
	
	if ( event.keyCode == 8 )
    {
        return false;
    }
    else if ( event.keyCode == 13 ) 
	{ 
		return false; 
	} 
}

function clear_input( $id )
{
	var e = document.getElementById( $id );
	if ( null != e )
	{
		e.name = $id;
		e.value = "";
	}
	return false;
}

function confirmLogout()
{
	return confirm( "Click OK to continue logging out" );
}

function display( $id )
{
	var e = document.getElementById( $id );
	if ( null != e )
	{
		e.style.display = "block";
	}
	return false;
}

function displayAs( $id, $type )
{
	var e = document.getElementById( $id );
	if ( null != e )
	{
		e.style.display = $type;
	}
	return false;
}

function hide( $id )
{
	var e = document.getElementById( $id );
	e.style.display = "none";
	return false;
}

function showAll( $name )
{
	var elements = document.getElementsByName( $name );
	for ( var element in elements )
	{
		if ( null != elements[element].style )
		{
			elements[element].style.display = "block";
		}
	}
	return false;
}

function hideAll( $name )
{
	var elements = document.getElementsByName( $name );
	for ( var element in elements )
	{
		if ( null != elements[element].style )
		{
			elements[element].style.display = "none";
		}
	}
	return false;
}

function hideDisplay( $name, $id )
{
	hideAll( $name );
	display( $id );
	return false;
}

function swap( $id1, $id2 )
{
	var d1 = document.getElementById( $id1 );
	var d2 = document.getElementById( $id2 );

	if ( null != d1 ) d1.style.display = "block";
	if ( null != d2 ) d2.style.display = "none";
	
	return false;
}

function toggle( $id )
{
	var e = document.getElementById( $id );
	
	if ( e.style.display == "none" )
	{
		e.style.display = "block";
	}
	else
	{
		e.style.display = "none";
	}
	return false;
}

function viewedit( $suffix )
{
	var view_id = "view_" + $suffix;
	var edit_id = "edit_" + $suffix;
	
	var view = document.getElementById( view_id );
	var edit = document.getElementById( edit_id );
	
	if ( view.style.display != "none" )
	{
		view.style.display = "none";
		edit.style.display = "block";
	}
	else
	{
		view.style.display = "block";
		edit.style.display = "none";
	}
	return false;
}

			function validate_form( $form_id )
			{
				$success = true;
				try
				{
					$form = document.getElementById( $form_id );

					$required = $form.getElementsByTagName( "SPAN" );
					for ( i in $required )
					{
						$e = $required[i];
						//alert( $e.tagName );
						if ( "SPAN" == $e.tagName )
						{
							if ( "required" == $e.className )
							{
								$e.style.visibility = "hidden";
							}
						}
					}
				
					$inputs = $form.getElementsByTagName( "input" );
					for ( i in $inputs )
					{
						$input = $inputs[i];
						$success = validate_input( $input ) && $success;
					}
					$selects = $form.getElementsByTagName( "select" );
					for ( i in $selects )
					{
						$select = $selects[i];
						$success = validate_select( $select ) && $success;
					}
				}
				catch ( err )
				{
					alert( err );
				}
				if ( ! $success )
				{
					//alert( "Please fill in fields marked: REQUIRED." );
					return false;
				} else {
					return true;
				}
			}

				function validate_input( $input )
				{
					if ( "INPUT" == $input.tagName )
					{
						//alert( "An Input" );
						if ( "req" == $input.id.substr( 0, 3 ) )
						{
							//alert( "Required" );
							if ( "" == $input.value )
							{
								$name = "required_" + $input.name;
								$span = document.getElementById( $name );
								//alert( $name );
								$span.style.visibility = "visible";
								return false;
							}
						}
					}
					return true;
				}

				function validate_select( $select )
				{
					//alert( "validate_select" );
					if ( "SELECT" == $select.tagName )
					{
						//alert( "A Select" );
						if ( "req" == $select.id.substr( 0, 3 ) )
						{
							//alert( "Required" );
							//alert( $select.options[$select.selectedIndex] );
							$name = "required_" + $select.name;
							//alert( $name );
							$option = $select.options[$select.selectedIndex]
							if ( "" == $option.value )
							{
								$span = document.getElementById( $name );
								$span.style.visibility = "visible";
								return false;
							}
						}
					}
					return true;
				}

			function check_passwords( $form_id )
			{
				$success = false;
				try
				{
					$new1 = document.getElementById( "req_new_password" );
					$new2 = document.getElementById( "req_new_password2" );
					$span = document.getElementById( "mismatch" );

					$success = ($new1.value == $new2.value);
					
					if ( ! $success )
					{
						$span.style.visibility = "visible";
					}
				}
				catch ( err )
				{
					alert( err );
				}
				return $success;
			}
