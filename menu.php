<?php
 ini_set("include_path", "./includes");    

// include function files for this application
require_once('functions_#PWD.php');

// JClark 2014-12-05 updates to PHP - need to know if session already in progess
// session_start() and POST data is available...
$session_start_ok = session_start();
 
if ($session_start_ok && isset($_POST['username'])){

    //create short variable names
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];



    if ($username && $passwd) {
    // they have just tried logging in
      try  {
        login($username, $passwd);
        // if they are in the database register the user id
        $_SESSION['valid_user'] = $username;
      }
      catch(Exception $e)  {
        // unsuccessful login
        do_html_header('Problem:');
        echo 'You could not be logged in.
              You must be logged in to view this page.';
        do_html_url('login.php', 'Login');
        do_html_footer();
        exit;
      }
    }
}

do_html_header('Home');
check_valid_user();
// 
if ($url_array = get_user_urls($_SESSION['valid_user'])) {
  display_user_urls($url_array);
}

// Find out if user is an admin 
$adminFlag = isAdmin($_SESSION['valid_user']);
// give menu of options
display_user_menu($adminFlag);

do_html_footer();
?>
