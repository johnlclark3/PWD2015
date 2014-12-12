<?php

    // PWD: ovrride the include path
    ini_set("include_path", "./includes"); 

    // PWD: Special include
    require_once('functions_#PWD.php'); 

    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";

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

    $table = 'race_registration';


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

    // Look to see if auto-refresh shoudl be on.. if so...
    if ($config['autoRefreshON'] == 'YES') {

        // Incremenent the count for number of displays made
        // - if time to return to display video clip, go to that display now
        $_SESSION['autoRefreshControl#2'] = $_SESSION['autoRefreshControl#2'] + 1;
        if ($_SESSION['autoRefreshControl#2'] > $config['autoRefreshRacerPointsCount']) {
            $_SESSION['autoRefreshControl#2'] = 0;
            echo '<meta http-equiv="refresh" content="0; URL=race_heat_nextracers_display.php">';   
        }
    }

    // Get description for race identified by session variable $_SESSION['raceToEnterResults']
    $dbconn = dbu_factory($config['db']);
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);
    $sql = "SELECT idRace, idHeat_Header, Description, idRacer1, idRacer2, idRacer3, Race_prefix FROM race where idRace = ".$_SESSION['raceToEnterResults'];
    $resultRACE = $dbconn->db_get_one_row($sql);
    $raceDescription = $resultRACE['Description']; 
    $racePrefix = $resultRACE['Race_prefix'];   

    // Get the last heat run for this race
    $lastRaceHeat = 0;
    $sql = "SELECT max(Number), idRace_Heat FROM race_heat where (idRace = ".$_SESSION['raceToEnterResults'].") and (Place_1 <> 0)";
    $lastHeatResult = $dbconn->db_get_one_row($sql);
    if ($lastHeatResult['max(Number)'] <> NULL) {
        // There IS a "last heat run" 
        $lastRaceHeat = $lastHeatResult['max(Number)'];
    }

    // Get names of top three racers in race (recognizing there may be a tie and someone is not selected yet..)
    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$resultRACE['idRacer1'];
    $result = $dbconn->db_get_one_row($sql);
    $place1 = $result['First_name']." ".$result['Last_name'];

    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$resultRACE['idRacer2'];
    $result = $dbconn->db_get_one_row($sql);
    $place2 = $result['First_name']." ".$result['Last_name'];

    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$resultRACE['idRacer3'];
    $result = $dbconn->db_get_one_row($sql);
    $place3 = $result['First_name']." ".$result['Last_name'];      

    // Get number of heats from heat_header
    $sqlHH = "SELECT Heats FROM heat_header where idHeat_Header=".$resultRACE['idHeat_Header'];
    $resultHH = $dbconn->db_get_one_row($sqlHH);

    // Output panel heading (force refresh if turned on)

    $panel_heading = ("Race Scores: ".$raceDescription."  (".$lastRaceHeat. " of ".$resultHH['Heats']." heats complete)");         
    if ($config['autoRefreshON'] == 'YES')  {
        do_html_header_refresh($panel_heading, $config['autoRefreshDuration']);
    } else    {
        do_html_header($panel_heading);  
    }
    check_valid_user();  

    $scheme = '';
    $fielddef = array(
    'f0' => array(FLD_ID => true, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => false, FLD_INPUT_TYPE => 'hidden',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'idRace_registration'),
    'f1' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'rrRace'),
    'f2' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Racer', FLD_DISPLAY_SZ => 30,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'rrRacer'),
    'f3' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Car number', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'rrCarNumber'),
    'f4' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Points earned', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'rrPointsEarned'),
    'f5' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'rrPlace')
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
            if (($mode != 's') && ($mode != 'i') && ($mode != 'u') && ($mode != 'd')) {
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
        case 's':
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'rrCarNumber', "rrRace = ".$_SESSION['raceToEnterResults']);
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'rrCarNumber', "rrRace = ".$_SESSION['raceToEnterResults']); 
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'rrCarNumber', "rrRace = ".$_SESSION['raceToEnterResults']); 
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'rrCarNumber', "rrRace = ".$_SESSION['raceToEnterResults']); 
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
                    foreach($fielddef as $fkey=>$fld) {

                        switch ($fld[FLD_DATABASE]) {

                            // If this is the race field - hide it; if no value, assgn the session value for the race
                            case 'rrRace':

                                // Be sure to have database value ready
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                                // Assign session race value if needed
                                if (($val == NULL) or ($val == 0)) 
                                    $val = $_SESSION['raceToEnterResults'];

                                // Punch out  HTML
                                echo "<tr><td></td>";  
                            echo "<td><input name = 'f1' type=hidden value=\"$val\" /></td></tr>";
                            break;

                            // If this is the "Racer" field, show a drop down list of racers in the den level that 
                            // are eligible for tihs race
                        case 'rrRacer':

                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                            // Punch out start of HTML
                            echo "<tr><td>Racer registering</td>";
                            echo "<td><select name = 'f2'>\n";

                            // Assemble a list of possible racers
                            $sql = "SELECT * from racer order by Last_name";
                            $result = $dbconn->db_get_all_rows($sql);
                            if (!$result) {
                                echo "falure </br>"; 
                            } else {
                                foreach($result as $key => $value) {
                                    $racerArray[$value[idRacer]] = $value[First_name]." ".$value[Last_name];
                                }
                            }

                            // Output each individual drop down entry
                            foreach ($racerArray as $key => $Choice){
                                echo "<option value='$key' ";

                                // If the entry is the same as the existing database value
                                // indicate this entry should show (selected)
                                // (used for existing database records)
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

                                // Initialize new field to simplify entry
                                if ($val == NULL)
                                    $val = 0;

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
            $table_1 = "<table cellpadding=\"".$config['rrd_t1_cellpadding']."\" cellspacing=\"".$config['rrd_t1_cellspacing']."\" border=\"".$config['rrd_t1_border']."\" bgcolor=\"".$config['rrd_t1_bgcolor']."\"><tr><td>";
            $table_2 = "<table cellpadding=\"".$config['rrd_t2_cellpadding']."\" cellspacing=\"".$config['rrd_t2_cellspacing']."\" border=\"".$config['rrd_t2_border']."\" bgcolor=\"".$config['rrd_t2_bgcolor']."\" class=\"datatable\">";

        ?>
        <form name="ActionForm" method="post" action="">

            <?php echo $table_1; ?>

            <?php echo $table_2; ?>

            <!--    <table cellpadding="1" cellspacing="0" border="0" bgcolor="#ababab"><tr><td> -->
            <!--                <table cellpadding="0" cellspacing="1" border="0" class="datatable"> -->
            <tr>
                <?php  // DATA HEADER
                    foreach ($fielddef as $fkey=>$fld) {
                        if ($fld[FLD_DISPLAY]) {
                            // $wd = isset($fld[FLD_DISPLAY_SZ]) ? " style=\"width: $fld[FLD_DISPLAY_SZ]ex\"" : "";
                            $wd = " align=\"center\"";
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
                echo "<tr$bk>";
                $tableFontSize = $config['rrdTableFontSize'];  
                foreach ($fielddef as $fkey=>$fld) {


                    if ($fld[FLD_VISIBLE]) {
                        $value =  htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                        switch ($fld[FLD_DATABASE]) {  

                            case 'rrRacer';
                                // Get description for racer identified by foreign key "rrRacer"
                                $racerSql = "SELECT First_name, Last_name FROM racer where idRacer =".$value;
                                $racerResult = $dbconn->db_get_one_row($racerSql);
                                $value = $racerResult['First_name']." ".$racerResult['Last_name'];
                                echo "<td  style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$value</b></font></td>"; 
                                break;
                            case 'rrCarNumber';
                                // just center value
                                echo "<td align=center style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$racePrefix$value</b></font></td>"; 
                                break;
                            case 'rrPointsEarned';
                                // just center value
                                echo "<td align=center style=\"background-color:$rowColor\"><font size = \"$tableFontSize\"><b>$value</b></font></td>"; 
                                break;
                            default:
                                if (!isset($value))
                                    $value = "&nbsp;";
                                echo "<td><font size = \"$tableFontSize\"><b>$value</b></font></td>";
                        }
                    }
                }
                echo "</tr>\n";
                $checked = '';
            }
        ?>
        </table>
        </td></tr></table>

    </form>

    <?php
        echo ("<h5><u>Race leaders:</u>&nbsp;&nbsp;"."#1: ".$place1."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        echo ("#2: ".$place2."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        echo ("#3: ".$place3."</h5>");
    }
    // PWD: Output footer
    //do_html_footer(); 
?>
