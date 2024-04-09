<?php
//<------start contents of common_functions.php

//function for formatting data to be passed to MySQL
function escape_string($value)
{
    $real_escape_string_exists = function_exists("mysqli_real_escape_string");

	if($real_escape_string_exists)
	{
        $value = mysqli_real_escape_string($GLOBALS['db']->link, $value);
	}
	else
	{
        $value = addslashes($value);
	}

	return $value;
	}

	//function for renaming files for safe usage
	function format_filename($filename)
	{
        $filename = trim(strtolower($filename));
        $filename = str_replace(' ', '-', $filename);
        $filename = str_replace(array("'", "\""), '', $filename);

        return $filename;
	}

//<------end contents of common_functions.php