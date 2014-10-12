<?php
/*
  values frequently used by other php files
  change here and they change everywhere
  NOTE: if you are in the template.php file, copy it to includes/constants.php and change values
  ALSO: don't be an idiot and commit your db username and password
*/
  define('DB_HOST', '');//hostname goes here
  define('DB_USER', '');//DB username goes here
  define('DB_PASS', '');//DB password goes here
  define('DB_NAME', 'menext');//name of DB in MYSQL
  define('PRE_SALT', "");//for security(be random)
  define('POST_SALT', "");//see above
  define('API_ARDUINO_KEY', "");//API key to use with actual hat
  define('ADMIN', '');//admin username for auto-generated admin account
  define('PASSWORD', '');//admin password for auto-generated admin account
?>
