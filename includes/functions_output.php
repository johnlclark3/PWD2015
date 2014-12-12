<?php

    ini_set("include_path", "./includes");   
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";         

    function do_html_header($title) {

        global $config; 

        // print an HTML header
    ?>
    <html>
    <head>
        <link rel="stylesheet" type="text/css" href="PWDstylesheet.css">
        <title><?php echo $title;?></title>
    </head>
    <body>
    <img src="images/aut01.jpg" alt="PWD logo" border="0"
        align="left" height="55" width="57" />
    <h1><?php echo $config['Page_Heading'] ?></h1>
    <hr />
    <?php
        if($title) {
            do_html_heading($title);
        }
    }

    function do_html_header_refresh($title, $delay) {

        global $config;   
        $delayText = "content=\"".$delay."\""; 
        // print an HTML header that refreshes periodically
    ?>
    <html>
        <head>
            <meta http-equiv="refresh" <?php echo $delayText ?>>
            <link rel="stylesheet" type="text/css" href="PWDstylesheet.css">
            <title><?php echo $title;?></title>
        </head>
        <body>
        
            <img src="images/aut01.jpg" alt="PWD logo" border="0"
                align="left" align="bottom" height="55" width="57" />
            <h1><?php echo $config['Page_Heading'] ?></h1>
            <hr />
            <?php
                if($title) {
                    do_html_heading($title);
                }
            }


            function do_html_footer() {
                // print an HTML footer
            ?>
        </body>
        <hr />   
        <!-- Show "logged in" info if user is logged in -->
        <?php if (check_loggedin()) { ?>
            <a href="menu.php">Home</a>&nbsp;&nbsp; 
            <a href="logout.php">Logout</a>&nbsp;&nbsp; 
            <a href="javascript:window.print()">Print</a>&nbsp;&nbsp;
            Logged in as <?php echo $_SESSION['valid_user'] ."." ?>   &nbsp;&nbsp;
            <?php
                if (isAdmin($_SESSION['valid_user']) == 'Y') {
                    echo "&nbsp;&nbsp;<b><font size=1 color=\"#FF0000\"> ** You are logged in as an ADMINISTRATOR** </b></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  

                }
                $a = getdate();
                printf('%s %d, %d &nbsp;&nbsp;%d:%d', $a['month'], $a['mday'], $a['year'], $a['hours'], $a['minutes']);
                echo "<br />";
            }
        ?>


    </html>  
    <?php
    }

    function do_html_heading($heading) {
        // print heading
    ?>
    <h2><?php echo $heading;?></h2>
    <?php
    }

    function do_html_URL($url, $name) {
        // output URL as link and br
    ?>
    <br /><a href="<?php echo $url;?>"><?php echo $name;?></a><br />
    <?php
    }

    function display_site_info() {
        // display some marketing info
    ?>
    <!-- <ul>
    <li>Store your bookmarks online with us!</li>
    <li>See what other users use!</li>
    <li>Share your favorite links with others!</li>
    </ul> -->
    <?php
    }

    function display_login_form() {
    ?>
    <p><a href="formRegister.php">Not a member?</a></p>
    <form method="post" action="menu.php">
        <table bgcolor="#cccccc">
            <tr>
            <td colspan="2">Members log in here:</td>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username"/></td></tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="passwd"/></td></tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Log in"/></td></tr>
            <tr>
                <td colspan="2"><a href="forgot_form.php">Forgot your password?</a></td>
            </tr>
        </table></form>
    <?php
    }

    function display_registration_form() {
    ?>
    <form method="post" action="register_new.php">
        <table bgcolor="#cccccc">
            <tr>
                <td>Email address:</td>
                <td><input type="text" name="email" size="30" maxlength="100"/></td></tr>
            <tr>
                <td>Preferred username <br />(max 16 chars):</td>
                <td valign="top"><input type="text" name="username"
                        size="16" maxlength="16"/></td></tr>
            <tr>
                <td>Password <br />(between 6 and 16 chars):</td>
                <td valign="top"><input type="password" name="passwd"
                        size="16" maxlength="16"/></td></tr>
            <tr>
                <td>Confirm password:</td>
                <td><input type="password" name="passwd2" size="16" maxlength="16"/></td></tr>
            <tr>
                <td colspan=2 align="center">
                    <input type="submit" value="Register"></td></tr>
        </table></form>
    <?php

    }

    function display_user_urls($url_array) {
        // display the table of URLs

        // set global variable, so we can test later if this is on the page
        global $bm_table;
        $bm_table = true;
    ?>
    <br />
    <form name="bm_table" action="delete_bms.php" method="post">
        <table width="300" cellpadding="2" cellspacing="0">
            <?php
                $color = "#cccccc";
                echo "<tr bgcolor=\"".$color."\"><td><strong>Bookmark</strong></td>";
                echo "<td><strong>Delete?</strong></td></tr>";
                if ((is_array($url_array)) && (count($url_array) > 0)) {
                    foreach ($url_array as $url)  {
                        if ($color == "#cccccc") {
                            $color = "#ffffff";
                        } else {
                            $color = "#cccccc";
                        }
                        //remember to call htmlspecialchars() when we are displaying user data
                        echo "<tr bgcolor=\"".$color."\"><td><a href=\"".$url."\">".htmlspecialchars($url)."</a></td>
                        <td><input type=\"checkbox\" name=\"del_me[]\"
                        value=\"".$url."\"/></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td>No bookmarks on record</td></tr>";
                }
            ?>
        </table>
    </form>
    <?php
    }

    function display_user_menu($adminFlag) {
        // display the menu options on this page
        // - Options just for administrators
        // - Options for everyone!
    ?>
    <hr />
    <?php
        // get user type to find out if they are an administrator

    ?>
    <a href="table_race.php">Work with Races</a><br />   
    <a href="table_racer.php">Update Racer master file</a><br />   


    <a href="table_den.php">Update Den master file</a><br /><br> 




    <br />
    <?php if ($adminFlag == 'Y')   {
            echo "<a href=\"table_heat_header.php\">Update Race template master file</a><br />";
            echo "<a href=\"table_valid_emails.php\">Update Valid emails</a><br />";
            echo "<a href=\"table_user.php\">Update User master file</a><br />";
            echo "<a href=\"init.php\">** Initialize database **</a><br />";
        }
    ?>

    <br>
    <a href="change_passwd_form.php">Change password</a><br /> 

    <?php
    }

    function display_add_bm_form() {
        // display the form for people to ener a new bookmark in
    ?>
    <form name="bm_table" action="add_bms.php" method="post">
        <table width="250" cellpadding="2" cellspacing="0" bgcolor="#cccccc">
            <tr><td>New BM:</td>
                <td><input type="text" name="new_url" value="http://"
                        size="30" maxlength="255"/></td></tr>
            <tr><td colspan="2" align="center">
                    <input type="submit" value="Add bookmark"/></td></tr>
        </table>
    </form>
    <?php
    }

    function display_password_form() {
        // display html change password form
    ?>
    <br />
    <form action="change_passwd.php" method="post">
    <table width="250" cellpadding="2" cellspacing="0" bgcolor="#cccccc">
        <tr><td>Old password:</td>
            <td><input type="password" name="old_passwd"
                    size="16" maxlength="16"/></td>
        </tr>
        <tr><td>New password:</td>
            <td><input type="password" name="new_passwd"
                    size="16" maxlength="16"/></td>
        </tr>
        <tr><td>Repeat new password:</td>
            <td><input type="password" name="new_passwd2"
                    size="16" maxlength="16"/></td>
        </tr>
        <tr><td colspan="2" align="center">
                <input type="submit" value="Change password"/>
            </td></tr>
    </table>
    <br />
    <?php
    }

    function display_forgot_form() {
        // display HTML form to reset and email password
    ?>
    <br />
    <form action="forgot_passwd.php" method="post">
    <table width="250" cellpadding="2" cellspacing="0" bgcolor="#cccccc">
        <tr><td>Enter your username</td>
            <td><input type="text" name="username" size="16" maxlength="16"/></td>
        </tr>
        <tr><td colspan=2 align="center">
                <input type="submit" value="Change password"/>
            </td></tr>
    </table>
    <br />
    <?php
    }

    function display_recommended_urls($url_array) {
        // similar output to display_user_urls
        // instead of displaying the users bookmarks, display recomendation
    ?>
    <br />
    <table width="300" cellpadding="2" cellspacing="0">
        <?php
            $color = "#cccccc";
            echo "<tr bgcolor=\"".$color."\">
            <td><strong>Recommendations</strong></td></tr>";
            if ((is_array($url_array)) && (count($url_array)>0)) {
                foreach ($url_array as $url) {
                    if ($color == "#cccccc") {
                        $color = "#ffffff";
                    } else {
                        $color = "#cccccc";
                    }
                    echo "<tr bgcolor=\"".$color."\">
                    <td><a href=\"".$url."\">".htmlspecialchars($url)."</a></td></tr>";
                }
            } else {
                echo "<tr><td>No recommendations for you today.</td></tr>";
            }
        ?>
    </table>
    <?php
    }

?>
