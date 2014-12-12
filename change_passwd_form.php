<?php
 
  ini_set("include_path", "./includes");    
 
 require_once('functions_#PWD.php'); 
 
 session_start();
 do_html_header('Change password');
 check_valid_user();
 
 display_password_form();

 do_html_footer();
?>
