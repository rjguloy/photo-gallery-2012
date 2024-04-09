<?php
//<------start contents of class_database.php

/*
*
*This class will simply start the database connection
*
*@author Robert John Guloy <bobguloy_is@yahoo.com>
*@copyright 2012 Robert John Guloy
*@license http://www.php.net/license/3_0.txt
*/

class Database
{
    var $link;

	public function connect($db_host=DB_HOST, $db_user=DB_USER, $db_pass=DB_PASS, $db_name=DB_NAME)
	{
	    $this->link = mysqli_connect($db_host, $db_user, $db_pass);

        if ( ! $this->link)
        {
            $_SESSION['sys_message'] = mysql_error();
            return FALSE;
        }

        if ( ! mysqli_select_db($this->link, $db_name))
        {
            $_SESSION['sys_message'] = mysql_error();
            return FALSE;
        }

        return TRUE;
    }
}

//<------end contents of class_database.php