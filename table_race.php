<?php

    // PWD: ovrride the include path
    ini_set("include_path", "./includes"); 

    // PWD: Special include
    require_once('functions_#PWD.php'); 

    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";
    require_once "constants.inc.php";     

    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";

    define('INP_MODE', 'mode');
    define('INP_START', 'start');


    define('ERR_INVALID_REQUEST', '<html><body>Invalid request.
    Click <a href="?mode=s">here</a> to return to main page.</body></html>');
    define('ERR_NO_KEY', '<html><body>Could not proceed. This form requires a key field that will uniquelly identify records in the table</body></html>');
    define('MSG_UPDATED', "Record has been updated successfully.
    Click <a href=\"?mode=s&amp;start=%d\">here</a> to return to main page.");
    define('MSG_INSERTED', 'Record has been added successfully.
    Click <a href="?mode=s&amp;start=-1">here</a> to return to main page.');
    define('MSG_DELETED', "Record has been deleted successfully.
    Click <a href=\"?mode=s&amp;start=%d\">here</a> to return to main page.");

    $table = 'race';


    //PWD: added a function here to more gracefully handle SQL errors - and punch them back towards the "main" panel.. may not be appropriate 
    //     for every SQL error, so it is not used everywhere... 
    // JClark 2014-12-11 Replaced <form> line in errorHander2 - eliminate varibale and hardcode form
    function errorHandler() {    
    ?>        
    <form method="LINK" ACTION="<?php "table_".$table.".php"?>">    
        <input type="submit" value="Go back">
    </form>

    <?php  
    }

    /* Function errorHandler2
    *  Purpose: send user a message and return themn to the main table
    * 
    * 
    */

    function errorHandler2($errorMessage) {    
    ?>     
    <body>
        <h5><?php echo ($errorMessage); ?></h5>
    </body>   
    <form method="LINK" ACTION="table_race.php">    
        <input type="submit" value="Go back">
    </form>

    <?php  
    }

    //PWD: add logic here to force authentication if not done already - and move heading output to this spot
    session_start();
    $panel_heading = ucfirst("Work with Races");
    do_html_header($panel_heading);
    check_valid_user();  

    // extract "levels" that should, be shown in main race list; if no value, 
    // set variable to show that level
    if (!isset($_SESSION['listTiger']))     $_SESSION['listTiger']      = 'checked';
    if (!isset($_SESSION['listWolf']))      $_SESSION['listWolf']       = 'checked';     
    if (!isset($_SESSION['listBear']))      $_SESSION['listBear']       = 'checked';     
    if (!isset($_SESSION['listW1']))        $_SESSION['listW1']         = 'checked';     
    if (!isset($_SESSION['listW2']))        $_SESSION['listW2']         = 'checked';     
    if (!isset($_SESSION['listSibling']))   $_SESSION['listSibling']    = 'checked';     
    if (!isset($_SESSION['listAdult']))     $_SESSION['listAdult']      = 'checked';    

    // Assign "where" clause to be used in select statements for filling the table
    $where = "";
    if ($_SESSION['listTiger'] == 'checked') {
        $where = "(Den_level = 'Tiger')";
    }

    if ($_SESSION['listWolf'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Wolf')";
    }

    if ($_SESSION['listBear'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Bear')";
    }

    if ($_SESSION['listW1'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Webelos I')";
    }

    if ($_SESSION['listW2'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Webelos II')";
    }

    if ($_SESSION['listSibling'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Sibling')";
    }

    if ($_SESSION['listAdult'] == 'checked') {
        if ($where <> "") {
            $where = $where." or ";
        }
        $where = $where." (Den_level = 'Adult')";
    }


    $scheme = '';
    $fielddef = array(
    'f0' => array(FLD_ID => true, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => false, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'idRace'),
    'f1' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Description', FLD_DISPLAY_SZ => 80,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 80, FLD_INPUT_MAXLEN => 80, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'Description'),
    'f2' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Race status', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 20, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'Race_status'),
    'f3' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 5,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 5, FLD_INPUT_MAXLEN => 5, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Tracks'),
    'f4' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'idHeat_Header'),
    'f5' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'idRacer1'),
    'f6' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'idRacer2'),
    'f7' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'idRacer3'),
    'f8' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Den level', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 20, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'Den_level'),
    'f9' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Prefix', FLD_DISPLAY_SZ => 3,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 3, FLD_INPUT_MAXLEN => 3, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'Race_prefix'),
    );


    $show_data = false;
    $show_input = false;
    $show_message = false;
    $message = NULL;
    $start = 0;
    $fld_indices_notempty = NULL;
    $fld_indices_Email = NULL;
    $fld_indices_Alpha = NULL;
    $fld_indices_AlphaNum = NULL;
    $fld_indices_Numeric = NULL;
    $fld_indices_Float = NULL;
    $fld_indices_Date = NULL;
    $fld_indices_Time = NULL;

    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        $mode = isset($_GET[INP_MODE]) ? $_GET[INP_MODE] : 's';
        if (($mode != 's') && ($mode != 'i') && ($mode != 'u')) {
            dbu_handle_error(ERR_INVALID_REQUEST);
        }
    } else if (isset($_POST[INP_MODE])) {
            $mode = $_POST[INP_MODE];
            if (($mode != 'i2') && ($mode != 'u2')) {
                dbu_handle_error(ERR_INVALID_REQUEST);
            }
    } else if (isset($_GET[INP_MODE])) {
            $mode = $_GET[INP_MODE];
            if (($mode != 's') && ($mode != 'i') && ($mode != 'u') && ($mode != 'd')&&( $mode != 'r')&&( $mode != 'e')&&( $mode != 'h')) {         
                dbu_handle_error(ERR_INVALID_REQUEST);
            }
    } else {
        dbu_handle_error(ERR_INVALID_REQUEST);
    }

    $keys = dbu_get_keys($fielddef);
    if (!$keys) {
        dbu_handle_error(ERR_NO_KEY);
    }
    $idx = 0;
    foreach($fielddef as $fkey=>$fld) {
        if ($fld[FLD_INPUT]) {
            if ($fld[FLD_INPUT_NOTEMPTY]) {
                if (!empty($fld_indices_notempty)) $fld_indices_notempty .= ', ';
                $fld_indices_notempty .= $idx;
            }
            if (!empty($fld[FLD_INPUT_VALIDATION])) {
                $name = "fld_indices_" . $fld[FLD_INPUT_VALIDATION];
                if (isset(${$name})) ${$name} .= ', ';
                ${$name} .= $idx;
            }
        }
        $idx++;
    }

    $dbconn = dbu_factory($config['db']);
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);

    switch ($mode) {   
        case 'r':
            // fetch the record the user selected; assign to a session variable 
            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $_POST, $keys);    
            $_SESSION['raceToRegister'] = $row['f0']; 
            // Keep track of race selected for radio button memory
            $_SESSION['raceSelection'] = $row['f0'];
            // Check to validate that the race has a heat_header assigned; if not - error
            //if ($row['f4'] == 0) {
            //    errorHandler2('Registration may not start until a heat is assigned to the race.  Try again later!');
            //    break;  
            //}
            $show_input = false;  
            $show_data = false;
            // run the race registration script
            echo '<meta http-equiv="refresh" content="0; URL=table_race_registration.php">'; 
            break;   

        case 'e':
            // fetch the record the user selected; assign to a session variable 
            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $_POST, $keys);    
            $_SESSION['raceToEnterResults'] = $row['f0']; 
            // Keep track of race selected for radio button memory
            $_SESSION['raceSelection'] = $row['f0'];
            $show_input = false;  
            $show_data = false;
            // run the race scoring script
            echo '<meta http-equiv="refresh" content="0; URL=table_race_heat.php">'; 
            break;   
        case 'h':
            // fetch the record the user selected; assign to a session variable 
            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $_POST, $keys);    
            $_SESSION['raceToEnterResults'] = $row['f0']; 
            // Keep track of race selected for radio button memory
            $_SESSION['raceSelection'] = $row['f0'];
            $show_input = false;  
            $show_data = false;
            // run the heat results display script
            echo '<meta http-equiv="refresh" content="0; URL=table_race_heat_display.php">'; 
            break;   
        case 's':
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'description', $where);
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());
            $show_data = true;
            break;
        case 'i':
        // Case = insert
            $row = dbu_get_input_defaults($fielddef);
            $nextmode = 'i2';
            $show_input = true;
            // Keep track of race selected for radio button memory
            // JClark 2014-12-10 - Not needed for insert - comment out next line
            // $_SESSION['raceSelection'] = $row['f0'];
            break;
        case 'i2':

            // Don;t care what they selected - race must always be inserted with a status of "Registering"
            // If status changed to "Started" do some editing
            $_POST['f2'] = "Registering";

            $rslt = dbu_handle_insert($fielddef, $scheme, $table, $dbconn, $_POST);
            if ($rslt) {
                $show_message = true;
                $message = MSG_INSERTED;
            } else {
                // PWD: Replaced call to function "dbu_handle_error" with "dbu_handle_error2"
                //      as well as output of a button to return to main table page
                // dbu_handle_error($dbconn->db_lasterror());
                dbu_handle_error2($dbconn->db_lasterror());
                errorHandler();
                break;
            }
            // PWD: Do not close DB connection!  Comment out next line
            // $dbconn->db_close();
            // PWD: Start of added logic to support re-display of table
            $nextmode = 's';
            $show_message = false;
            $show_data = true;
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'description', $where);   
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());     
            // PWD: End of added logic to support re-display of table
            break;
        case 'u':

            // Recalculate scores for full race
            recomputeRacerScores($_POST['RKEY']);

            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $_POST, $keys);
            $nextmode = 'u2';
            $show_input = true;
            // Keep track of race selected for radio button memory
            $_SESSION['raceSelection'] = $row['f0'];
            break;
        case 'u2':

            // Count the racers registered for this race
            $racersRegistered = 0;
            $sqlRR = "SELECT * FROM race_registration WHERE rrRace =".$_POST['RKEY'];     
            $rsltRR = $dbconn->db_get_all_rows($sqlRR);
            if ($rsltRR) $racersRegistered = count($rsltRR);

            // Do a count on the number of heats run so far for this race
            $heatsRaced = 0;
            $sqlHR = "SELECT * FROM race_heat WHERE ((race_heat.idRace =".$_POST['RKEY'].") and (Place_1 <> 0))";     
            $rsltHR = $dbconn->db_get_all_rows($sqlHR);
            if ($rsltHR) $heatsRaced = count($rsltHR);

            // Get the number of racers defined for this race in the heat_header
            $racersInRace = 0;
            $sqlRIR = "SELECT Racers FROM heat_header WHERE idHeat_Header =".$_POST['f4'];     
            $rsltRIR = $dbconn->db_get_one_row($sqlRIR);
            if ($rsltRIR) $racersInRace = $rsltRIR['Racers'];

            // If status changed to "Started" do some editing
            // if ($HTTP_POST_VARS['f2'] == "Started") {
            if ($_POST['f2'] == "Started") {  
                // Need to have a heat_header assigned
                if (($_POST['f4'] == 0) or ($racersInRace == 0)) {
                    errorHandler2("Race must have correct heat assignment before you start the race.  Race not updated.  Review heat assignment and try again.");
                    break;
                }

                // Deal with too many racers registered for race
                if ($racersRegistered > $racersInRace) {
                    errorHandler2("Too many racers registered for the race.  Race not updated.  Review heat assignment and try again.");
                    break;
                }
                // Deal with not enough racers registered for race
                if ($racersRegistered < $racersInRace) {
                    errorHandler2("Not enough racers registered for the race.  Race not updated.  Review heat assignment and try again.");
                    break;
                }
                // Ensure that there all car numbers are less thanor equal to the number of cars
                // expected in this race.  Could have higher car numbers assigned if racers registered
                // before heat assignment made

                $badCarNumber = 'N';
                foreach($rsltRR as $key => $value) {
                    if ($value['rrCarNumber'] > $racersInRace) $badCarNumber = 'Y';
                }
                if ($badCarNumber == 'Y') {
                    errorHandler2("Review car numbers for registered racers - there is one that needs to be reassigned before starting the race.  Race not updated.");
                    break;                    
                }
            }

            // If status changed to "Registering" do some editing
            // if ($HTTP_POST_VARS['f2'] == "Registering") {
            if ($_POST['f2'] == "Registering") {  
                // If there are heats already run, do not go back to "Registering" status
                if ($heatsRaced > 0) {
                    errorHandler2("The race status cannot be \"Registering\" if the race is in progress.  Race not updated.");
                    break;                    
                }
                // If there are any race_heat records, purge them now
                $sqlRH = "DELETE from race_heat WHERE idRace=".$_POST['RKEY'];     
                $rsltRH = $dbconn->db_query($sqlRH);
            }

            $rslt = dbu_handle_update($fielddef, $scheme, $table, $dbconn, $_POST, $keys);
            if ($rslt) {
                $show_message = true;
                $message = sprintf(MSG_UPDATED, $start);
            } else {
                dbu_handle_error($dbconn->db_lasterror());
            }

            // If race is "starting" make sure we have race_heat records ready to go
            //if ($HTTP_POST_VARS['f2'] == 'Started')  {
            if ($_POST['f2'] == 'Started')  { 
                // See if race_heat records already exist
                // $raceHeatSql = "SELECT * from race_heat where idRace=".$HTTP_POST_VARS['RKEY'];
                $raceHeatSql = "SELECT * from race_heat where idRace=".$_POST['RKEY'];
                $raceHeatResult = $dbconn->db_get_all_rows($raceHeatSql);

                if (sizeof($raceHeatResult) == 0) {
                    // If records do not exist, get array of race heat template records
                    // $heatSql = "SELECT * from heat where Heat_Header_idHeat_Header =".$HTTP_POST_VARS['f4']." order by Number";
                    $heatSql = "SELECT * from heat where Heat_Header_idHeat_Header =".$_POST['f4']." order by Number";
                    $heatResult = $dbconn->db_get_all_rows($heatSql);

                    // read through array of race heat template records - and punch out
                    //   a race_heat record for each
                    if (!$heatResult) {
                        echo "falure 100 </br>"; 
                    } else {
                        $i=1;
                        foreach($heatResult as $key => $value) {
                            //echo "Car:".$value[Car_Lane1];

                            // go get the record from the "race_registration" file that has the same car#; 
                            //  we will use the racer id# we find here to write out in the new record

                            // Get racer id for car in lane #1
                            // $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$HTTP_POST_VARS['RKEY'].") and (rrCarNumber=".$value[Car_Lane1].")";
                            $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$_POST['RKEY'].") and (rrCarNumber=".$value['Car_Lane1'].")";
                            $rrResult = $dbconn->db_get_all_rows($rrSql);
                            if (!$rrResult) {
                                echo "failure 101 </br>";
                            } else {
                                foreach($rrResult as $rrKey => $rrValue) {
                                    $lane1Racer = $rrValue['rrRacer'];    
                                }   
                            }
                            // Get racer id for car in lane #2
                            // $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$HTTP_POST_VARS['RKEY'].") and (rrCarNumber=".$value[Car_Lane2].")";
                            $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$_POST['RKEY'].") and (rrCarNumber=".$value['Car_Lane2'].")";
                            $rrResult = $dbconn->db_get_all_rows($rrSql);
                            if (!$rrResult) {
                                echo "failure 102 /br>";
                            } else {
                                foreach($rrResult as $rrKey => $rrValue) {
                                    $lane2Racer = $rrValue['rrRacer'];    
                                }   
                            }
                            // Get racer id for car in lane #3
                            // $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$HTTP_POST_VARS['RKEY'].") and (rrCarNumber=".$value[Car_Lane3].")";
                            $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$_POST['RKEY'].") and (rrCarNumber=".$value['Car_Lane3'].")";
                            $rrResult = $dbconn->db_get_all_rows($rrSql);
                            if (!$rrResult) {
                                echo "failure 103 </br>";
                            } else {
                                foreach($rrResult as $rrKey => $rrValue) {
                                    $lane3Racer = $rrValue['rrRacer'];    
                                }   
                            }
                            // Get racer id for car in lane #4
                            $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$_POST['RKEY'].") and (rrCarNumber=".$value['Car_Lane4'].")";
                            // $rrSql = "SELECT rrRacer from race_registration where (rrRace=".$HTTP_POST_VARS['RKEY'].") and (rrCarNumber=".$value[Car_Lane4].")";
                            $rrResult = $dbconn->db_get_all_rows($rrSql);
                            if (!$rrResult) {
                                echo "failure 104 </br>";
                            } else {
                                foreach($rrResult as $rrKey => $rrValue) {
                                    $lane4Racer = $rrValue['rrRacer'];    
                                }   
                            }


                            // $raceHeatSql = "INSERT into race_heat values(0,".$HTTP_POST_VARS['RKEY'].",".$i++.",".$lane1Racer.",".$lane2Racer.",".$lane3Racer.",".$lane4Racer.",0,0,0,0)";                         
                            $raceHeatSql = "INSERT into race_heat values(0,".$_POST['RKEY'].",".$i++.",".$lane1Racer.",".$lane2Racer.",".$lane3Racer.",".$lane4Racer.",0,0,0,0)";                         
                            $raceHeatInsert = $dbconn->db_query($raceHeatSql);  

                        }
                    }

                }

            }

            // PWD: Do not close DB connection!  Comment out next line
            // $dbconn->db_close();
            $nextmode = 's';
            // PWD: Start of added logic to support re-display of table
            $show_message = false;
            $show_data = true;
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'description', $where);   
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());     
            // PWD: End of added logic to support re-display of table
            break;
        case 'd':
            // Delete all race_heat records
            $sqlRH = "DELETE from race_heat WHERE idRace=".$_POST['RKEY'];     
            $rsltRH = $dbconn->db_query($sqlRH);
            // Delete all race_registraiton records
            $sqlRR = "DELETE from race_registration WHERE rrRace=".$_POST['RKEY'];     
            $rsltRR = $dbconn->db_query($sqlRR);

            $rslt = dbu_handle_delete($fielddef, $scheme, $table, $dbconn, $_POST, $keys);
            if ($rslt) {
                $show_message = true;
                $message = sprintf(MSG_DELETED, $start);
            } else {
                dbu_handle_error($dbconn->db_lasterror());
            }
            // PWD: Do not close DB connection!  Comment out next line
            // $dbconn->db_close();
            // PWD: Start of added logic to support re-display of table
            $show_message = false;
            $show_data = true;
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'description', $where);   
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());     
            // PWD: End of added logic to support re-display of table
            $nextmode = 's';
            break;
    }


    // PWD: Output heading panel  (moved up with authentication)
    // $panel_heading = ucfirst($table." Maintenance");
    // do_html_header($panel_heading);


?>

<script  type="text/javascript">
    <!--
    function doslice(arg, idx) {
        var ret = Array();
        for (var i = idx; i < arg.length; i++) {
            ret.push(arg[i]);
        }
        return ret;
    }

    function Check(theForm, what, regexp, indices) {
        for (var i = 0; i < indices.length; i++) {
            var el = theForm.elements[indices[i]];
            if (el.value == "") continue;
            var avalue = el.value;
            if (!regexp.test(avalue)) {
                alert("Field is not a valid " + what);
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckEmail(theForm) {
        var regexp = /^[0-9a-z\.\-_]+@[0-9a-z\-\_]+\..+$/i;    
        return Check(theForm, "email", regexp, doslice(arguments, 1));
    }

    function CheckAlpha(theForm) {
        var regexp = /^[a-z]*$/i;
        return Check(theForm, "alpha value", regexp, doslice(arguments, 1));
    }

    function CheckAlphaNum(theForm) {
        var regexp = /^[a-z0-9]*$/i;
        return Check(theForm, "alphanumeric value", regexp, doslice(arguments, 1));
    }

    function CheckNumeric(theForm) {
        for (var i = 1; i < arguments.length; i++) {
            var el = theForm.elements[arguments[i] - 1];
            if (el.value == "") continue;
            var avalue = parseInt(el.value);
            if (isNaN(avalue)) {
                alert("Field is not a valid integer number");
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckFloat(theForm) {
        for (var i = 1; i < arguments.length; i++) {
            var el = theForm.elements[arguments[i]];
            if (el.value == "") continue;
            var avalue = parseFloat(el.value);
            if (isNaN(avalue)) {
                alert("Field is not a valid floating point number");
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckDate(theForm) {
        for (var i = 1; i < arguments.length; i++) {
            var el = theForm.elements[arguments[i]];
            if (el.value == "") continue;
            var avalue = el.value;
            if (isNaN(Date.parse(avalue))) {
                alert("Field is not a valid date");
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckTime(theForm) {
        var regexp = /^[0-9]+:[0-9]+:[0-9]+/i;    
        if (!Check(theForm, "time", regexp,  doslice(arguments, 1)))
            return false;                 
        for (var i = 1; i < arguments.length; i++) {
            var el = theForm.elements[arguments[i]];
            if (el.value == "") continue;
            var avalue = el.value;
            if (isNaN(Date.parse("1/1/1970 " + avalue))) {
                alert("Field is not a valid time");
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckRequiredFields(theForm) {    
        for (var i = 1; i < arguments.length; i++) {
            var el = theForm.elements[arguments[i]];
            if (el.value=="") {
                alert("This field may not be empty");
                el.focus();
                return false;
            }
        }
        return true;
    }

    function CheckForm(theForm) {
        return (
        CheckRequiredFields(theForm<?php echo isset($fld_indices_notempty) ? ", " . $fld_indices_notempty : "" ?>) &&
        CheckEmail(theForm<?php echo isset($fld_indices_Email) ? ", " . $fld_indices_Email : "" ?>) &&
        CheckAlpha(theForm<?php echo isset($fld_indices_Alpha) ? ", " . $fld_indices_Alpha : "" ?>) &&
        CheckAlphaNum(theForm<?php echo isset($fld_indices_AlphaNum) ? ", " . $fld_indices_AlphaNum : "" ?>) &&
        CheckNumeric(theForm<?php echo isset($fld_indices_Numeric) ? ", " . $fld_indices_Numeric : "" ?>) &&
        CheckFloat(theForm<?php echo isset($fld_indices_Float) ? ", " . $fld_indices_Float : "" ?>) &&
        CheckDate(theForm<?php echo isset($fld_indices_Date) ? ", " . $fld_indices_Date : "" ?>) &&
        CheckTime(theForm<?php echo isset($fld_indices_Time) ? ", " . $fld_indices_Time: "" ?>)
        )
    }

    <!--
    function confirmation() {
        var answer = confirm("Are you sure you want to remove this race and all its activity?")
        if (answer){
            //    window.location = "http://www.google.com/";
            document.forms.ActionForm.action='?mode=d'; document.forms.ActionForm.submit()
        }
        else{
            alert("Race not deleted.")
        }
    }

    //-->
</script>
</head>
<body>
<?php
    if ($show_message) {
    ?>
    <table cellpadding="1" cellspacing="0" border="0" bgcolor="#ababab"><tr><td>
                <table cellpadding="0" cellspacing="1" border="0" bgcolor="#ffffff"><tr><td>
                    <?php echo $message?>
                </table>
            </td></tr>
    </table>

    <?php
    } else if ($show_input) {
        ?>
        <form name="InputForm" method="post" enctype="multipart-form-data"
            onsubmit="return CheckForm(this)"
            action="">
            <table border="0">
                <?php  // INPUT

                    // Look to see if there are any racers registered for this race
                    // .. this count will be used below to permit/prevent update to fields
                    $racersRegistered = 0;
                    $heatsRaced = 0;
                    // Jclark 2014-12-10 - only get registered racers if not an insert
                    if ($mode != 'i') {
                        $sqlRR = "SELECT * FROM race_registration WHERE rrRace =".$row['f0'];     
                        $rsltRR = $dbconn->db_get_all_rows($sqlRR);
                        if ($rsltRR) $racersRegistered = count($rsltRR);
                    

                        // Do a count on the number of heats run so far for tihs race
                        // $heatsRaced = 0;
                        $sqlHR = "SELECT * FROM race_heat WHERE (idRace =".$row['f0'].") and (Place_1 <> 0)";     
                        $rsltHR = $dbconn->db_get_all_rows($sqlHR);
                        if ($rsltHR) $heatsRaced = count($rsltHR);
                    }

                // display number of racers in race and number of heats run so far
                echo "<tr><td>Racers Registered</td><td><b><font color=\"#FF0000\">".$racersRegistered."</font></b></td></tr>";                
                echo "<tr><td>Heats run so far</td><td><b><font color=\"#FF0000\">".$heatsRaced."</font></b></td></tr>";                

                foreach($fielddef as $fkey=>$fld) {

                    switch ($fld[FLD_DATABASE]) {
                        // Show a dropdown box of possible race statuses (from "constants" include file)
                        case 'Race_status':

                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                            // Punch out start of HTML
                            echo "<tr><td>Race status</td>";
                            echo "<td><select name = 'f2'>\n";

                            // Output each individual drop down entry
                            foreach ($raceStatus as $key => $choice){
                                echo "<option value='$key' ";

                                // If the entry is the same as the existing database value
                                // indicate this entry should show (selected)
                                // (used for existing database records)
                                if ($val == $key) {
                                    echo " selected";
                                }
                                echo " >$choice</option>\n";
                            }

                            echo "<select></td><tr>";

                            // Save current status 
                            $currentRaceStatus = $val;

                            break;

                        case 'Tracks':

                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                            // If any racers registerd, only show tracks as output field
                            if ($racersRegistered > 0) {
                                echo "<tr><td># of Tracks</td><td><b><font color=\"#FF0000\">".$val."</b></font></td><input type='hidden' name='f3' value='".$val."'/></tr>";
                            } 
                            else {         
                                // Punch out start of HTML
                                echo "<tr><td># of Tracks</td>";
                                echo "<td><select name = 'f3'>\n";
                                // Output each individual drop down entry
                                foreach ($trackSizes as $key => $choice){
                                    echo "<option value='$key' ";
                                    // If the entry is the same as the existing database value
                                    // indicate this entry should show (selected)
                                    // (used for existing database records)
                                    if ($val == $key) {
                                        echo " selected";
                                    }
                                    echo " >$choice</option>\n";
                                }
                                echo "<select></td><tr>";
                            }
                            break;

                        case 'idHeat_Header':

                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // If any heats run now OR if race started heat assignment is output only
                            if (($heatsRaced > 0) or (($row['f2'] <> 'Registering') and ($row['f2'] <> ''))) {
                                $sqlHH = "SELECT Description FROM heat_header WHERE heat_header.idHeat_Header=".$row{'f4'};     
                                $rsltHH = $dbconn->db_get_one_row($sqlHH);
                                if ($rsltHH) { 

                                    echo "<tr><td>Heat assignment</td><td><b><font color=\"#FF0000\">".$rsltHH['Description']."</b></font></td><input type='hidden' name='f4' value='".$val."'/></tr>";
                                } 
                            } else {         

                                // Punch out start of HTML
                                echo "<tr><td>Heat assignment</td>";
                                echo "<td><select name = 'f4'>\n";

                                // Assemble a list of possible heat assignments
                                $sql = "SELECT * from heat_header order by description";
                                $result = $dbconn->db_get_all_rows($sql);
                                if (!$result) {
                                    echo "falure </br>"; 
                                } else {
                                    foreach($result as $key => $value) {
                                        $heatHeaderArray[$value[idHeat_Header]] = $value[Description];
                                    }
                                }

                                // Need option for "not assigned" (zero  in database field)
                                echo "<option value = 0";
                                if (($val == NULL) or ($val == 0)) {
                                    echo " selected";
                                }
                                echo " >Not assigned</option>\n";

                                // Output each individual drop down entry
                                foreach ($heatHeaderArray as $heatKey => $heatChoice){
                                    echo "<option value='$heatKey' ";

                                    // If the entry is the same as the existing database value
                                    // indicate this entry should show (selected)
                                    // (used for existing database records)
                                    if ($val == $heatKey) {
                                        echo " selected";
                                    }
                                    echo " >$heatChoice</option>\n";
                                }

                                echo "<select></td><tr>";
                            }
                            break;                            

                        case 'idRacer1':
                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);      
                            if ($val == NULL) $val = 0;
                            $racerName = "";
                            // If racer value is non-zero, get racer name
                            if ($val > 0) {
                                $sql = "SELECT First_name, Last_name FROM racer WHERE idRacer=".$val;     
                                $rslt = $dbconn->db_get_one_row($sql);
                                // If any records found - only show heat as output field
                                if ($rslt) {   
                                    $racerName = $rslt['First_name']." ".$rslt['Last_name'];
                                }
                            }
                            // Punch out HTML - will always be output only
                            echo "<tr><td>1st Place racer</td><td><b><font color=\"#FF0000\">".$racerName."</b></font></td><input type='hidden' name='f5' value='".$val."'/></tr>";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                            break;                            

                        case 'idRacer2':
                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                            if ($val == NULL) $val = 0;
                            $racerName = "";
                            // If racer value is non-zero, get racer name
                            if ($val > 0) {
                                $sql = "SELECT First_name, Last_name FROM racer WHERE idRacer=".$val;     
                                $rslt = $dbconn->db_get_one_row($sql);
                                // If any records found - only show heat as output field
                                if ($rslt) {   
                                    $racerName = $rslt['First_name']." ".$rslt['Last_name'];
                                }
                            }
                            // Punch out HTML - will always be output only
                            echo "<tr><td>2nd Place racer</td><td><b><font color=\"#FF0000\">".$racerName."</b></font></td><input type='hidden' name='f6' value='".$val."'/></tr>";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                            break;                            

                        case 'idRacer3':
                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                            if ($val == NULL) $val = 0;
                            $racerName = "";
                            // If racer value is non-zero, get racer name
                            if ($val > 0) {
                                $sql = "SELECT First_name, Last_name FROM racer WHERE idRacer=".$val;     
                                $rslt = $dbconn->db_get_one_row($sql);
                                // If any records found - only show heat as output field
                                if ($rslt) {   
                                    $racerName = $rslt['First_name']." ".$rslt['Last_name'];
                                }
                            }
                            // Punch out HTML - will always be output only
                            echo "<tr><td>3rd Place racer</td><td><b><font color=\"#FF0000\">".$racerName."</b></font></td><input type='hidden' name='f7' value='".$val."'/></tr>";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                            break;                            

                        case 'Den_level':
                            // Be sure to have dtaabase value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // If any racers registerd, only show den level as output field
                            if ($racersRegistered > 0) {
                                echo "<tr><td>Den level</td><td><b><font color=\"#FF0000\">".$val."</b></font></td><input type='hidden' name='f8' value='".$val."'/></tr>";
                            } 
                            else {         
                                // Punch out start of HTML
                                echo "<tr><td>Den level</td>";
                                echo "<td><select name = 'f8'>\n";
                                // Output each individual drop down entry
                                foreach ($denLevel as $key => $choice)         {
                                    echo "<option value='$key' ";
                                    // If the entry is the same as the existing database value
                                    // indicate this entry should show (selected)
                                    // (used for existing database records)
                                    if ($val == $key) {
                                        echo " selected";
                                    }
                                    echo " >$choice</option>\n";
                                }
                                echo "<select></td><tr>";
                            }
                            break;

                        default:    

                            if ($fld[FLD_INPUT]) {
                                echo "<tr><td>$fld[FLD_DISPLAY]</td>";
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                switch ($fld[FLD_INPUT_TYPE]) {
                                    case "textarea":
                                        echo "<td><textarea name=\"$fkey\" cols=\"$fld[FLD_INPUT_SZ]\" rows=\"15\">$val</textarea></td></tr>";
                                        break;
                                    case "hidden":
                                        echo "<td><input name=\"$fkey\" type=\"$fld[FLD_INPUT_TYPE]\" value=\"$val\" /></td></tr>";
                                        break;
                                    case "select":
                                        echo "<td>". WriteCombo(${$fkey . '_values'}, $fkey, "") ."</td></tr>";
                                        break;
                                    default:
                                        echo "<td><input name=\"$fkey\" type=\"$fld[FLD_INPUT_TYPE]\" size=\"$fld[FLD_INPUT_SZ]\" maxlength=\"$fld[FLD_INPUT_MAXLEN]\" value=\"$val\" /></td></tr>";
                                }
                            }
                    }
                }
            ?>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" value="Save" /></td>
            </tr>
        </table>
        <input type="hidden" name="mode" value="<?php echo $nextmode;?>" />
        <?php // KEY
            if(isset($_POST[RKEY])) {
                $key = $_POST[RKEY];
                if (get_magic_quotes_gpc())
                    $key = stripslashes($key);
                echo "<input type='hidden' name='".RKEY."' value='".htmlentities($key, ENT_QUOTES, $config['encoding'])."' />";
            }
        ?>
    </form>


    <!-- PWD: Add logic to activate "Quit" button -->
    <form method="LINK" ACTION="<?php "table_".$table.".php"?>">
        <input type="submit" value=" Exit no update ">
    </form>    

    <?php } else if ($show_data) { ?>

        <form action="race_list_change.php" method="post">
            <input type="checkbox" name="listTiger" value="T" <?PHP print $_SESSION['listTiger']; ?>/>Tiger</>&nbsp
            <input type="checkbox" name="listWolf" value="W" <?PHP print $_SESSION['listWolf']; ?>/>Wolf</>&nbsp
            <input type="checkbox" name="listBear" value="B" <?PHP print $_SESSION['listBear']; ?>/>Bear</>&nbsp
            <input type="checkbox" name="listW1" value="W1" <?PHP print $_SESSION['listW1']; ?>/>Webelos I</>&nbsp
            <input type="checkbox" name="listW2" value="W2" <?PHP print $_SESSION['listW2']; ?>/>Webelos II</>&nbsp
            <input type="checkbox" name="listSibling" value="S" <?PHP print $_SESSION['listSibling']; ?>/>Sibling</>&nbsp
            <input type="checkbox" name="listAdult" value="A" <?PHP print $_SESSION['listAdult']; ?>/>Adult</>&nbsp
            <input type="submit" name="formSubmit" value="change selection" />
        </form>




        <form name="ActionForm" method="post" action="">
            <table cellpadding="1" cellspacing="0" border="0" bgcolor="#ababab"><tr><td>
                        <table cellpadding="0" cellspacing="1" border="0" class="datatable">
                            <tr><th style="width: 25px;"></th>
                                <?php  // DATA HEADER
                                    foreach ($fielddef as $fkey=>$fld) {
                                        if ($fld[FLD_DISPLAY]) {
                                            $wd = isset($fld[FLD_DISPLAY_SZ]) ? " style=\"width: $fld[FLD_DISPLAY_SZ]ex\"" : "";
                                            echo "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                                        }
                                }
                            ?>
                        </tr>
                        <?php  // DATA
                            $checked = ' checked="checked"';
                            $i = 0;
                            foreach($rows as $row) {
                                $bk = $i++ % 2 ? "" : ' class="sublight"';    


                                // If this record is the last one to have any maintenance - locate selection button here
                                // anticipating it will be maintained again
                                if (isset($_SESSION['raceSelection'])) {
                                    if ($_SESSION['raceSelection'] == $row[RKEY]) {
                                        $checked = ' checked="checked"'; 
                                    }
                                }

                                echo "<tr$bk><td><input type='radio'$checked name='".RKEY."' value='".htmlentities($row[RKEY], ENT_QUOTES, $config['encoding'])."' /></td>";
                                foreach ($fielddef as $fkey=>$fld) {
                                    if ($fld[FLD_VISIBLE]) {
                                        $value =  htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                        if (!isset($value))
                                            $value = "&nbsp;";
                                        echo "<td>$value</td>";
                                    }
                                }
                                echo "</tr>\n";
                                $checked = '';
                            }
                        ?>
                    </table>
                </td></tr></table><br />
        <?php // PAGER
            if (isset($pager[PG_PAGES])) {
                if (isset($pager[PG_PAGE_PREV])) {
                    echo "<a href=\"?mode=s&amp;start=$pager[PG_PAGE_PREV]\">Prev</a>&nbsp;";
                } else {
                    echo "Prev&nbsp;";
                }
                foreach($pager[PG_PAGES] as $pg => $st) {
                    if ($st != $start) {
                        echo "<a href=\"?mode=s&amp;start=$st\">$pg</a>&nbsp;";
                    } else {
                        echo "<b>$pg</b>&nbsp;";
                    }
                }
                if (isset($pager[PG_PAGE_NEXT])) {
                    echo "<a href=\"?mode=s&amp;start=$pager[PG_PAGE_NEXT]\">Next</a>&nbsp;";
                } else {
                    echo "Next&nbsp;";
                }
                echo "<br />";
            }
        ?>
        <br />
        <table cellpadding="1" cellspacing="0" border="0" bgcolor="#ababab"><tr><td>
                    <table cellpadding="1" cellspacing="0" border="0" bgcolor="#fcfcfc"><tr><td>
                                <input type="button" value="new race" onclick="document.forms.ActionForm.action='?mode=i'; document.forms.ActionForm.submit()" />&nbsp;
                                <input type="button" value="update" onclick="document.forms.ActionForm.action='?mode=u'; document.forms.ActionForm.submit()" />&nbsp;
                                <input type="button" value="delete" onclick="confirmation()" />&nbsp;
                                <input type="button" value="register" onclick="document.forms.ActionForm.action='?mode=r'; document.forms.ActionForm.submit()" />&nbsp;  
                                <input type="button" value="enter results" onclick="document.forms.ActionForm.action='?mode=e'; document.forms.ActionForm.submit()" />&nbsp;        
                                <input type="button" value="results (auto-refreshing)" onclick="document.forms.ActionForm.action='?mode=h'; document.forms.ActionForm.submit()" />        
                            </td></tr>
                    </table>
                </td></tr>
        </table>
    </form>

    <!--PWD: Add "exit button -->
    <form method="LINK" ACTION="menu.php">
        <input type="submit" value="  Exit   ">
    </form>

    <?php }

    // PWD: Output footer
    do_html_footer(); 

    // </body>
    // </html>
?>