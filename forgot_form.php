<?php

    ini_set("include_path", "./includes");  
    require_once('functions_#PWD.php');   
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php"; 


    do_html_header('Reset password');

    display_forgot_form();

    do_html_footer();
?>
