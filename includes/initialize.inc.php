<?php
//<------start contents of initialize.inc.php

/*
*
*This file will initialize the required classes, functions, and constants
*
*@author Robert John Guloy <bobguloy_is@yahoo.com>
*@copyright 2012 Robert John Guloy
*@license http://www.php.net/license/3_0.txt
*
*/

//Start the variables for $_SESSION
session_start();

// Include the website configuration constants
if (file_exists('includes/config.inc.php')) include_once('includes/config.inc.php');


//<------end contents of initialize.inc.php