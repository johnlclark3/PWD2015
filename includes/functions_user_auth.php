<?php

    // JClark - changes noted below
    ini_set("include_path", "./includes"); 
    require_once('functions_database.php');
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";     

    // JClark add isValidEmail
    // Purpose: Check the "Valid_emails" database for an occurance of
    //          the email adddress typed in.  If not there, they may not join the website
    // Returns: True if email address was found
    //          False if email address was not found for any reason
    function isValidEmail($email) {

        // connect to db
        $conn = db_connect();

        // attempt to retrieve record for this email address
        // If error - or no records, return "false"
        $result = $conn->query("select * from valid_emails where Eml_address='".$email."'");
        if ((!$result) or ($result->num_rows==0)) {
            return false;
        }
        return true;
    }

    function register($username, $email, $password) {
        // register new person with db
        // return true or error message

        // connect to db
        $conn = db_connect();

        // check if username is unique
        $result = $conn->query("select * from user where username='".$username."'");
        if (!$result) {
            throw new Exception('Could not execute query');
        }

        if ($result->num_rows>0) {
            throw new Exception('That username is taken - go back and choose another one.');
        }

        // if ok, put in db
        $insertStatement = "insert into user values(0, '".$username."', sha1('".$password."'), 'N', '".$email."')";
        $result = $conn->query($insertStatement);
        if (!$result) {
            throw new Exception('Could not register you in database - please try again later.');
        }

        return true;
    }

    function login($username, $password) {
        // check username and password with db
        // if yes, return true
        // else throw exception

        // connect to db
        $conn = db_connect();

        // check if username is unique
        $result = $conn->query("select * from user
        where username='".$username."'
        and passwd = sha1('".$password."')");
        if (!$result) {
            throw new Exception('Could not log you in.');
        }

        if ($result->num_rows>0) {

            // Set session varaible id user is an admin user or ont
            $_SESSION['admin_user'] = 'N'; 
            if (isAdmin($username) == 'Y') $_SESSION['admin_user'] = 'Y'; 
            return true;
        } else {
            throw new Exception('Could not log you in.');
        }
    }

    function check_loggedin() { 
        // see if somebody is logged in - return true or false
        if (isset($_SESSION['valid_user']))  {
            return true;
        } else {
            return false;

        }
    }

    function check_valid_user()  {         
        // see if somebody is logged in and notify them if not
        if (isset($_SESSION['valid_user']))  {
            //   echo "Logged in as ".$_SESSION['valid_user'].".<br />";  (now shows on page footer)

        } else {
            // they are not logged in
            // JClark - replaced next two lines with the follwoing one line
            // do_html_heading('Problem:');
            // echo 'You are not logged in.<br />';
            do_html_heading('You are not logged in.');
            do_html_url('login.php', 'Login');
            do_html_footer();
            exit;
        }
    }

    /* Function: isAdmin
    * - return the "Y" if th euser is an admin
    * - any other value is NOT an admin
    */ 
    function isAdmin($username)  {         

        $conn = db_connect();
        $result = $conn->query("select admin from user
        where username='".$username."'");
        if (!$result) {
            throw new Exception('Could not find user record isAdmin function.');
        } else if ($result->num_rows == 0) {
                throw new Exception('Could not find user record in isAdmin function.');
            } else {
                $row = $result->fetch_object();
                $admin = $row->admin;
        }
        return $admin;
    }

    function change_password($username, $old_password, $new_password) {
        // change password for username/old_password to new_password
        // return true or false

        // if the old password is right
        // change their password to new_password and return true
        // else throw an exception
        login($username, $old_password);
        $conn = db_connect();
        $result = $conn->query("update user
        set passwd = sha1('".$new_password."')
        where username = '".$username."'");
        if (!$result) {
            throw new Exception('Password could not be changed.');
        } else {
            return true;  // changed successfully
        }
    }
        function assign_password($username, $new_password) {
        // change password for username/
        // return true or false

        $conn = db_connect();
        $result = $conn->query("update user
        set passwd = sha1('".$new_password."')
        where username = '".$username."'");
        if (!$result) {
            throw new Exception('Password could not be changed.');
        } else {
            return true;  // changed successfully
        }
    }

    function get_random_word($min_length, $max_length) {

        global $config;

        // grab a random word from dictionary between the two lengths
        // and return it

        // generate a random word
        $word = '';
        // remember to change this path to suit your system
        // $dictionary = '/usr/dict/words';  // the ispell dictionary      
        $dictionary = $config['dictionary'];

        $fp = @fopen($config['dictionary'], 'r');
        if(!$fp) {
            return false;
        }
        $size = filesize($dictionary);

        // go to a random location in dictionary
        $rand_location = rand(0, $size);
        fseek($fp, $rand_location);

        // get the next whole word of the right length in the file
        while ((strlen($word) < $min_length) || (strlen($word)>$max_length) || (strstr($word, "'"))) {
            if (feof($fp)) {
                fseek($fp, 0);        // if at end, go to start
            }
            $word = fgets($fp, 80);  // skip first word as it could be partial
            $word = fgets($fp, 80);  // the potential password
        }
        $word = trim($word); // trim the trailing \n from fgets
        return $word;
    }

    function reset_password($username) {
        // set password for username to a random value
        // return the new password or false on failure
        // get a random dictionary word b/w 6 and 13 chars in length
        $new_password = get_random_word(6, 13);

        if($new_password == false) {
            throw new Exception('Could not generate new password.');
        }

        // add a number  between 0 and 999 to it
        // to make it a slightly better password
        $rand_number = rand(0, 999);
        $new_password .= $rand_number;

        // set user's password to this in database or return false
        $conn = db_connect();
        $result = $conn->query("update user
        set passwd = sha1('".$new_password."')
        where username = '".$username."'");
        if (!$result) {
            throw new Exception('Could not change password.');  // not changed
        } else {
            return $new_password;  // changed successfully
        }
    }

    function notify_password($username, $password) {
        // notify the user that their password has been changed

        $conn = db_connect();
        $result = $conn->query("select email from user
        where username='".$username."'");
        if (!$result) {
            throw new Exception('Could not find email address.');
        } else if ($result->num_rows == 0) {
                throw new Exception('Could not find email address.');
                // username not in db
            } else {
                $row = $result->fetch_object();
                $email = $row->email;
                $from = "From: johnlclark3@yahoo.com \r\n";
                $mesg = "Your Pinewood derby password has been changed to ".$password.".\r\n"
                ."  Please change it next time you log in.\r\n";

                if (mail($email, 'Pinewood derby login information', $mesg, $from)) {
                    return true;
                } else {
                    throw new Exception('Could not send email.');
                }
        }
    }

?>
