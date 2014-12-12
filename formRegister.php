<?php
 
 ini_set("include_path", "./includes");    

 require_once('functions_#PWD.php');
 do_html_header('User Registration');

 display_registration_form();

 do_html_footer();
?>