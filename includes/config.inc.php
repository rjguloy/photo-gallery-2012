<?php
//<------start contents of config.inc.php

//Database Constants
define ('DB_HOST', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'localhost' : 'localhost'));
define ('DB_USER', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'root' : 'some_server_username'));
define ('DB_PASS', ($_SERVER['SERVER_NAME'] === 'localhost' ? '' : 'some_server_password'));
define ('DB_NAME', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'db_gallery': 'some_server_db'));

//Website URL Configs
define ('WEBSITE_PATH', 'http://localhost/');
define ('ABSOLUTE_PATH', getcwd() . '/');
//Get the webpage filename
define ('PAGE_FILE', basename($_SERVER['PHP_SELF']));

//Define the Directory paths for the gallery images and thumbnails
//Use relative paths and do not add the WEBSITE_PATH
define('IMAGE_PATH', 'gallery/');
define('THUMB_PATH', 'gallery/thumbs/');

//<------end contents of config.inc.php