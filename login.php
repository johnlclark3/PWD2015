<?php
 ini_set("include_path", "./includes");  
 
 require_once('functions_#PWD.php');
 require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";  
 // require_once$_SERVER['COMPUTERNAME'] . ".config.inc.php";  
 do_html_header('');

 display_site_info(); 
 display_login_form();

 do_html_footer();
?>