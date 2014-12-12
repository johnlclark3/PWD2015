<?php
 
 ini_set("include_path", "./includes"); 

require_once "config.inc.php";

require_once "db_utils.inc";
require_once "db_". $config['db'] . ".inc";

// PWD: Special include
require_once('functions_#PWD.php'); 
 
 
 notify_password('jj', '123123');
 
               $from = "From: johnlclark3@yahoo.com \r\n";
  mail('jclark@atonllc.com', 'Subject', 'message', $from);
  
?>
