<?php
//<------start contents of config.inc.php

//Database Constants
define ('DB_HOST', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'localhost' : ${{ secrets.DB_HOSTNAME }}));
define ('DB_USER', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'root' : ${{ secrets.DB_USER }}));
define ('DB_PASS', ($_SERVER['SERVER_NAME'] === 'localhost' ? '' : ${{ secrets.DB_PASS }}));
define ('DB_NAME', ($_SERVER['SERVER_NAME'] === 'localhost' ? 'db_gallery': 'db_gallery'));

//Website URL Configs
define ('WEBSITE_PATH', $_SERVER['HTTP_HOST'] /*'http://localhost/'*/);
define ('ABSOLUTE_PATH', getcwd() . '/');
//Get the webpage filename
define ('PAGE_FILE', basename($_SERVER['PHP_SELF']));

//Define the Directory paths for the gallery images and thumbnails
//Use relative paths and do not add the WEBSITE_PATH
define('IMAGE_PATH', 'gallery/');
define('THUMB_PATH', 'gallery/thumbs/');

//<------end contents of config.inc.php