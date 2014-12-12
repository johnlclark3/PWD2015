<?php

    // PWD: ovrride the include path
    ini_set("include_path", "./includes"); 

    // PWD: Special include
    require_once('functions_#PWD.php');
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php"; 

    // require_once "config.inc

    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";
    require_once "constants.inc.php";  


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

    $table = 'race_heat_video';


    //PWD: added a function here to more gracefully handle SQL errors - and punch them back towards the "main" panel.. may not be appropriate 
    //     for every SQL error, so it is not used everywhere... 
    function errorHandler() {    
    ?>        
    <form method="LINK" ACTION="<?php "table_".$table.".php"?>">    
        <input type="submit" value="Go back">
    </form>

    <?php  
    }


    //PWD: add logic here to force authentication if not done already - and move heading output to this spot
    session_start();
    $panel_heading = "Assign Heat Video Clip";
    do_html_header($panel_heading);
    check_valid_user();  


    $scheme = '';
    $fielddef = array(
    'f0' => array(FLD_ID => true, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => false, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'idRace_Heat_Video'),
    'f1' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'RHVidRace_Heat'),
    'f2' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Video clip name', FLD_DISPLAY_SZ => 100,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 100, FLD_INPUT_MAXLEN => 136, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => '',
    FLD_DATABASE => 'Video_name')
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


    // Get description for race identified by session variable $_SESSION['raceToEnterResults']
    $sql = "SELECT Description FROM race where idRace = ".$_SESSION['raceToEnterResults'];
    $result = $dbconn->db_get_one_row($sql);
    $raceDescription = $result['Description'];
    // Get number of heat identified by session variable $_SESSION['raceHeatForVideo']       
    $sql = "SELECT Number FROM race_heat where idRace_Heat = ".$_SESSION['raceHeatForVideo'];
    $result = $dbconn->db_get_one_row($sql);
    $heatNumber = $result['Number'];

    // Be sure to assign proper mode... 
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        if (isset($_GET[INP_MODE])) {
            $mode = $_GET[INP_MODE];
        } else {
            // Look to see if a record already exists; if it does, go into insert mode.
            // Otherwise go into update mode
            $sql = "SELECT * FROM race_heat_video where RHVidRace_Heat = ".$_SESSION['raceHeatForVideo'];
            $result = $dbconn->db_get_one_row($sql);
            if (!$result) {
                $mode = 'i';
            } else {
                $mode = 'u';
                $recordKey = $result['idRace_Heat_Video'];
            }

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



    echo "<h3>Race: <FONT COLOR=\"ff0000\">*** ".$raceDescription." ***&nbsp;&nbspHeat: ".$heatNumber."</FONT></h3>";


    // Get list of ALL video clips in directory
    unset($dropDown);
    $listOfFiles = dirList($config['videoClipDirectory2'] );
    foreach ($listOfFiles as $key=>$value) {
        // if this file is not already assigned to another heat, add to arrary of "available clips"
        $sql = "SELECT * FROM race_heat_video where Video_name ='".$value."'";
        $result = $dbconn->db_get_one_row($sql);
        if ((!$result) or ($result[RHVidRace_Heat] == $_SESSION['raceHeatForVideo'])) {
            $dropDown[$value] = $value;
        }
    }


    // should only be one record returned...
    //if (!$result) {
    //    echo "falure </br>"; 
    //} else {
    //    foreach($result as $key => $value) {
    //        $raceDescription = $value['Description'];
    //    }
    //}

    switch ($mode) {
        case 's':
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager);
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
            // Close database connection and return to the race_heat display
            $dbconn->db_close();
            echo '<meta http-equiv="refresh" content="0; URL=table_race_heat.php">';   
            break;
        case 'u':
            $JC_POST['RKEY'] = "".$recordKey."";
            $row = dbu_fetch_by_key($fielddef, $scheme, $table, $dbconn, $JC_POST, $keys);
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
            // Close database connection and return to the race_heat display
            $dbconn->db_close();
            echo '<meta http-equiv="refresh" content="0; URL=table_race_heat.php">';  
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager);
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
                            case 'Video_name':
                                // Be sure to have database value ready
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                // Punch out start of HTML
                                echo "<tr><td>Video clip name</td>";
                                echo "<td><select name = 'f2'>\n";
                                // Output each individual drop down entry
                                foreach ($dropDown as $key => $choice){
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
                            break;
                        case 'RHVidRace_Heat':  
                            // Be sure to have database value ready
                            $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                            // assign input value of race_heat 
                            if (!$val) {
                                $val =   $_SESSION['raceHeatForVideo'];
                            }
                            echo "<tr><td>$fld[FLD_DISPLAY]</td>"; 
                            echo "<td><input name=\"$fkey\" type=\"hidden\" value=\"$val\" /></td></tr>";
                            break;
                        default:
                            if ($fld[FLD_INPUT]) {
                                echo "<tr><td>$fld[FLD_DISPLAY]</td>";
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                switch ($fld[FLD_INPUT_TYPE]) {
                                    case "textarea":
                                          
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
                <td><input type="submit" value="save" /></td>
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
    
            <!--Button to remove entry from database -->
        </br><form method="LINK" ACTION="removeRaceHeatVideo.php">
            <input type="submit" value="remove video clip">
        </form>             


    <!-- PWD: Add logic to activate "Quit" button -->
    <form method="LINK" ACTION="table_race_heat.php">
        <input type="submit" value=" Exit no update ">
    </form>    

    <?php } else if ($show_data) { ?>
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
                                <input type="button" value="insert" onclick="document.forms.ActionForm.action='?mode=i'; document.forms.ActionForm.submit()" />&nbsp;
                                <input type="button" value="update" onclick="document.forms.ActionForm.action='?mode=u'; document.forms.ActionForm.submit()" />&nbsp;
                                <input type="button" value="delete" onclick="document.forms.ActionForm.action='?mode=d'; document.forms.ActionForm.submit()" />
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

?>