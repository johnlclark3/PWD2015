<?php

    ini_set("include_path", "./includes"); 
    require_once('functions_#PWD.php'); 
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";
    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";



    //PWD: added a function here to more gracefully handle SQL errors - and punch them back towards the "main" panel.. may not be appropriate 
    //     for every SQL error, so it is not used everywhere... 
    function errorHandler() {    
    ?>        
    <form method="LINK" ACTION="<?php "table_".$table.".php"?>">    
        <input type="submit" value="Go back">
    </form>

    <?php  
    }

    session_start();       

    // Get description for race identified by session variable $_SESSION['raceToEnterResults']

    $dbconn = dbu_factory($config['db']);
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);
    $sql = "SELECT idRace, idHeat_Header, Description, idRacer1, idRacer2, idRacer3 FROM race where idRace = ".$_SESSION['raceToEnterResults'];
    $resultRACE = $dbconn->db_get_one_row($sql);
    $raceDescription = $resultRACE['Description']; 

    // Get highest heat completed so far... 

    // Get number of heats from heat_header
    $sqlHH = "SELECT Heats FROM heat_header where idHeat_Header=".$resultRACE['idHeat_Header'];
    $resultHH = $dbconn->db_get_one_row($sqlHH);


    // Output panel heading (force refresh if turned on)

    $panel_heading = ("Race Results: ".$raceDescription."  (".$resultHH['Heats']." heats)"); 
    if ($config['autoRefreshON'] == 'YES')  {
        do_html_header_refresh($panel_heading, $config['autoRefreshDuration']);
    } else {
        do_html_header($panel_heading);    
    }

    check_valid_user();  




    $scheme = '';
    $fielddef = array(
    'f0' => array(FLD_ID => true, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => false, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'idRace_Heat'),
    'f1' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'idRace'),
    'f2' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Heat', FLD_DISPLAY_SZ => 4,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Number'),
    'f3' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Racer_lane1'),
    'f4' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Racer_lane2'),
    'f5' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Racer_lane3'),
    'f6' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Racer_lane4'),
    'f7' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Place 1', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Place_1'),
    'f8' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Place 2', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Place_2'),
    'f9' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Place 3', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Place_3'),
    'f10' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Place 4', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 20, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Place_4')
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
            if (($mode != 's') && ($mode != 'i') && ($mode != 'u') && ($mode != 'd') && ($mode != 'h')) {
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
    /** @var dbconn */
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);


    switch ($mode) {
        case 's':
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "idRace=".$_SESSION['raceToEnterResults']);
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());
            $show_data = true;
            break;
        case 'i':
            $row = dbu_get_input_defaults($fielddef);
            $nextmode = 'i2';
            $show_input = true;
            break;
        case 'i2':
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "idRace=".$_SESSION['raceToEnterResults']);  
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());     
            // PWD: End of added logic to support re-display of table
            break;
        case 'u':
            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $_POST, $keys);
            $nextmode = 'u2';
            $show_input = true;
            break;
        case 'u2':
            $rslt = dbu_handle_update($fielddef, $scheme, $table, $dbconn, $_POST, $keys);
            if ($rslt) {
                $show_message = true;
                $message = sprintf(MSG_UPDATED, $start);
            } else {
                dbu_handle_error($dbconn->db_lasterror());
            }
            // PWD: Do not close DB connection!  Comment out next line
            // $dbconn->db_close();
            $nextmode = 's';
            // PWD: Start of added logic to support re-display of table
            $show_message = false;
            $show_data = true;
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "idRace=".$_SESSION['raceToEnterResults']);  
            if (!$rows && $dbconn->db_lasterror())
                dbu_handle_error($dbconn->db_lasterror());     
            // PWD: End of added logic to support re-display of table
            break;
        case 'd':
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "idRace=".$_SESSION['raceToEnterResults']);  
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

                    // Assemble a list of racer names in this heat
                    $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f3];
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane1RacerName = $value['First_name']." ".$value['Last_name'];    

                    $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f4];
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane2RacerName = $value['First_name']." ".$value['Last_name'];   

                    $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f5];
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane3RacerName = $value['First_name']." ".$value['Last_name'];

                    $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f6];
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane4RacerName = $value['First_name']." ".$value['Last_name'];

                    // Assemble a list of racer car numbers in this heat
                    $sql = "SELECT rrCarNumber from race_registration where (rrRacer=".$row[f3]." and rrRace=".$_SESSION['raceToEnterResults'].")";
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane1CarNumber = $value['rrCarNumber'];    

                    $sql = "SELECT rrCarNumber from race_registration where (rrRacer=".$row[f4]." and rrRace=".$_SESSION['raceToEnterResults'].")";
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane2CarNumber = $value['rrCarNumber'];    

                    $sql = "SELECT rrCarNumber from race_registration where (rrRacer=".$row[f5]." and rrRace=".$_SESSION['raceToEnterResults'].")";
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane3CarNumber = $value['rrCarNumber'];    

                    $sql = "SELECT rrCarNumber from race_registration where (rrRacer=".$row[f6]." and rrRace=".$_SESSION['raceToEnterResults'].")";
                    $result = $dbconn->db_get_all_rows($sql);
                    foreach($result as $key => $value) 
                        $lane4CarNumber = $value['rrCarNumber'];    

                    // Create an array of racers for drop down boxes
                    $racerChoice[0] = "";  
                    $racerChoice[$row[f3]] = "Car# ".$lane1CarNumber."   - ".$lane1RacerName;
                    $racerChoice[$row[f4]] = "Car# ".$lane2CarNumber."   - ".$lane2RacerName; 
                    $racerChoice[$row[f5]] = "Car# ".$lane3CarNumber."   - ".$lane3RacerName; 
                    $racerChoice[$row[f6]] = "Car# ".$lane4CarNumber."   - ".$lane4RacerName; 

                    foreach($fielddef as $fkey=>$fld) {

                        switch ($fld[FLD_DATABASE]) {


                            case 'idRace':
                                // If this is the race field - hide it; if no value, assign the session value for the race

                                // Be sure to have database value ready
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                                // Assign session race value if needed
                                if (($val == NULL) or ($val == 0)) 
                                    $val = $_SESSION['raceToEnterResults'];

                                // Punch out  HTML
                                echo "<tr><td></td>";  
                            echo "<td><input name = 'f1' type=hidden value=\" $val\" /></td></tr>";
                            break;

                        case 'Number': 
                            // Show heat sequence number (output only)
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']); 
                            echo "<tr><td>Race heat number:</td>";  
                            echo "<td>$val</td><td><input name = 'f2' type=hidden value=\" $val\" /></td></tr>";
                            break; 

                        case 'Racer_lane1': 
                            // Show racer name for lane 1 (output only)
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']); 
                            echo "<tr><td>Racer - Lane #1:</td>";  
                            echo "<td>$lane1RacerName</td><td>Car# $lane1CarNumber</td><td><input name = 'f3' type=hidden value=\" $val\" /></td></tr>";
                            break; 

                        case 'Racer_lane2': 
                            // Show racer name for lane 2 (output only)
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']); 
                            echo "<tr><td>Racer - Lane #2:</td>";  
                            echo "<td>$lane2RacerName</td><td>Car# $lane2CarNumber</td><td><input name = 'f4' type=hidden value=\" $val\" /></td></tr>";
                            break; 
                        case 'Racer_lane3': 
                            // Show racer name for lane 3 (output only)
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']); 
                            echo "<tr><td>Racer - Lane #3:</td>";  
                            echo "<td>$lane3RacerName</td><td>Car# $lane3CarNumber</td><td><input name = 'f5' type=hidden value=\" $val\" /></td></tr>";
                            break; 

                        case 'Racer_lane4': 
                            // Show racer name for lane 4 (output only)
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']); 
                            echo "<tr><td>Racer - Lane #4:</td>";  
                            echo "<td>$lane4RacerName</td><td>Car# $lane4CarNumber</td><td><input name = 'f6' type=hidden value=\" $val\" /></td></tr>";
                            break; 

                        case 'Place_1':
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // Punch out start of HTML
                            echo "<tr><td>First place racer:</td><td><select name = 'f7'>\n";

                            // Output each individual drop down entry using array built above for choices
                            foreach ($racerChoice as $key => $Choice){
                                echo "<option value='$key' ";

                                // indicate this entry should show (selected) if same as database value
                                if ($val == $key) {
                                    echo " selected";
                                }
                                echo " >$Choice</option>\n";
                            }
                            echo "<select></td><tr>";
                            break;                            

                        case 'Place_2':
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // Punch out start of HTML
                            echo "<tr><td>Second place racer:</td><td><select name = 'f8'>\n";

                            // Output each individual drop down entry using array built above for choices
                            foreach ($racerChoice as $key => $Choice){
                                echo "<option value='$key' ";

                                // indicate this entry should show (selected) if same as database value
                                if ($val == $key) {
                                    echo " selected";
                                }
                                echo " >$Choice</option>\n";
                            }
                            echo "<select></td><tr>";
                            break;                            

                        case 'Place_3':
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // Punch out start of HTML
                            echo "<tr><td>Third place racer:</td><td><select name = 'f9'>\n";

                            // Output each individual drop down entry using array built above for choices
                            foreach ($racerChoice as $key => $Choice){
                                echo "<option value='$key' ";

                                // indicate this entry should show (selected) if same as database value
                                if ($val == $key) {
                                    echo " selected";
                                }
                                echo " >$Choice</option>\n";
                            }
                            echo "<select></td><tr>";
                            break;                            

                        case 'Place_4':
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // Punch out start of HTML
                            echo "<tr><td>Fourth place racer:</td><td><select name = 'f10'>\n";

                            // Output each individual drop down entry using array built above for choices
                            foreach ($racerChoice as $key => $Choice){
                                echo "<option value='$key' ";

                                // indicate this entry should show (selected) if same as database value
                                if ($val == $key) {
                                    echo " selected";
                                }
                                echo " >$Choice</option>\n";
                            }
                            echo "<select></td><tr>";
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

    <?php } else if ($show_data) { 

            // contstuct   first "table" HTML line
            $table_1 = "<table cellpadding=\"".$config['rhd_t1_cellpadding']."\" cellspacing=\"".$config['rhd_t1_cellspacing']."\" border=\"".$config['rhd_t1_border']."\" bgcolor=\"".$config['rhd_t1_bgcolor']."\"><tr><td>";
            $table_2 = "<table cellpadding=\"".$config['rhd_t2_cellpadding']."\" cellspacing=\"".$config['rhd_t2_cellspacing']."\" border=\"".$config['rhd_t2_border']."\" bgcolor=\"".$config['rhd_t2_bgcolor']."\" class=\"datatable\">";

        ?>
        <form name="ActionForm" method="post" action="">

            <?php echo $table_1; ?>

            <?php echo $table_2; ?>

            <tr>
                <?php  // DATA HEADER
                    foreach ($fielddef as $fkey=>$fld) {
                        if ($fld[FLD_DISPLAY]) {
                            $wd = isset($fld[FLD_DISPLAY_SZ]) ? " style=\"text-align:center; width: $fld[FLD_DISPLAY_SZ]ex\"" : "";
                            echo "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                        }
                }
            ?>
        </tr>
        <?php  // DATA
            $checked = ' checked="checked"';
            $i = 0;
            $rowColor = "white";  
            foreach($rows as $row) {
                $bk = $i++ % 2 ? "" : ' class="sublight"';
                if ($rowColor == "white") {
                    $rowColor = "yellow";
                } else {
                    $rowColor = "white";
                }
                echo "<tr$bk >";
                $tableFontSize = $config['rhdTableFontSize'];  

                foreach ($fielddef as $fkey=>$fld) {

                    switch ($fld[FLD_DATABASE]) {

                        case 'Number';    
                            echo "<td style=\"background-color:$rowColor; text-align:center\"><font size = \"$tableFontSize\"><b>$row[f2]</b></font></td>";  
                            break;

                        case 'Place_1';
                            $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f7];
                            $result = $dbconn->db_get_all_rows($sql);
                            if ($result) {
                                foreach($result as $key => $value) 
                                    $lane1RacerName = $value['First_name']." ".$value['Last_name'];    
                            }    else {
                                $lane1RacerName = "";
                            }
                            echo "<td style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$lane1RacerName</b></font></td>";
                            break;

                        case 'Place_2';
                            $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f8];
                            $result = $dbconn->db_get_all_rows($sql);
                            if ($result) {
                                foreach($result as $key => $value) 
                                    $lane2RacerName = $value['First_name']." ".$value['Last_name'];    
                            }    else {
                                $lane2RacerName = "";
                            }
                            echo "<td style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$lane2RacerName</b></font></td>";
                            break;

                        case 'Place_3';
                            $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f9];
                            $result = $dbconn->db_get_all_rows($sql);
                            if ($result) {
                                foreach($result as $key => $value) 
                                    $lane3RacerName = $value['First_name']." ".$value['Last_name'];    
                            }    else {
                                $lane3RacerName = "";
                            }
                            echo "<td style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$lane3RacerName</b></font></td>";
                            break;

                        case 'Place_4';
                            $sql = "SELECT Last_name, First_name from racer where idRacer=".$row[f10];
                            $result = $dbconn->db_get_all_rows($sql);
                            if ($result) {
                                foreach($result as $key => $value) 
                                    $lane4RacerName = $value['First_name']." ".$value['Last_name'];    
                            }    else {
                                $lane4RacerName = "";
                            }
                            echo "<td style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$lane4RacerName</b></font></td>";
                            break;

                        default:


                            if ($fld[FLD_VISIBLE]) {
                                $value =  htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                if (!isset($value))
                                    $value = "&nbsp;";
                                echo "<td>$value</td>";
                            }
                    }
                }
                echo "</tr>\n";
                $checked = '';
            }
        ?>
        </table>
        </td></tr></table><br />
    </form>

    <?php
        echo ("<h5><u>Race leaders:</u>&nbsp;&nbsp;"."#1: ".$place1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        echo ("#2: ".$place2."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        echo ("#3: ".$place3."</h5>");
    }
    // PWD: Output footer
    //do_html_footer(); 
?>