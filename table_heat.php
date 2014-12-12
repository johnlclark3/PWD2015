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

    $table = 'heat';


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
    $panel_heading = ucfirst("Detail Heat Maintenance");        
    do_html_header($panel_heading);
    check_valid_user();  

    // Get current session value for reverse-lane-order
    if (!isset($_SESSION['laneOrder']))     $_SESSION['laneOrder'] = 'frontward';

    $scheme = '';
    $fielddef = array(
    'f0' => array(FLD_ID => true, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 7,
    FLD_INPUT => false, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => false, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'idHeat'),
    'f1' => array(FLD_ID => false, FLD_VISIBLE => false, FLD_DISPLAY => '', FLD_DISPLAY_SZ => 50,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 50, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Heat_Header_idHeat_Header'),
    'f2' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Heat #', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 7, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Number'),  
    'f3' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Car in Lane 1', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 10, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Car_Lane1'),
    'f4' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Car in Lane 2', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 10, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Car_Lane2'),
    'f5' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Car in Lane 3', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 10, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Car_Lane3'),
    'f6' => array(FLD_ID => false, FLD_VISIBLE => true, FLD_DISPLAY => 'Car in Lane 4', FLD_DISPLAY_SZ => 20,
    FLD_INPUT => true, FLD_INPUT_TYPE => 'text',
    FLD_INPUT_SZ => 10, FLD_INPUT_MAXLEN => 10, FLD_INPUT_DFLT => '',
    FLD_INPUT_NOTEMPTY => true, FLD_INPUT_VALIDATION => 'Numeric',
    FLD_DATABASE => 'Car_Lane4')
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
    /** @var dbconn */
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);

    // Get description for Heat Header identified by session variable $_SESSION['heatHeaderToEdit']
    $sql = "SELECT Description FROM heat_header where idHeat_Header = ".$_SESSION['heatHeaderToEdit'];
    $result = $dbconn->db_get_all_rows($sql);
    // should only be one record returned...
    if (!$result) {
        echo "falure </br>"; 
    } else {
        foreach($result as $key => $value) {
            $HHDescription = $value['Description'];
        }
    }

    echo "<h3>Race Template: <FONT COLOR=\"ff0000\">*** ".$HHDescription." ***</FONT></h3>";

    switch ($mode) {
        case 's':
            $pager=array();
            $start = (isset($_GET[INP_START]) && is_numeric($_GET[INP_START])) ? (int)$_GET[INP_START] : 0;
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "Heat_Header_idHeat_Header=".$_SESSION['heatHeaderToEdit']);
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "Heat_Header_idHeat_Header=".$_SESSION['heatHeaderToEdit']);
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "Heat_Header_idHeat_Header=".$_SESSION['heatHeaderToEdit']);
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
            $rows = dbu_handle_select($fielddef, $scheme, $table, $dbconn, $keys, $start, $config['rows_per_page'], $config['pager_items'], $pager, 'Number', "Heat_Header_idHeat_Header=".$_SESSION['heatHeaderToEdit']);
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
                            // If this is the Heat Header id field - hide it; if no value, assgn the session value 
                            case 'Heat_Header_idHeat_Header':
                                // Be sure to have database value ready
                                $val = htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);
                                // Assign session race value if needed
                                if (($val == NULL) or ($val == 0)) 
                                    $val = $_SESSION['heatHeaderToEdit'];
                                // Punch out  HTML
                                echo "<tr><td></td>";  
                            echo "<td><input name = 'f1' type=hidden value=\" $val\" /></td></tr>";
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
            unset ($outputArray);
        ?>

        <form name="ActionForm" method="post" action="">

            <table cellpadding="1" cellspacing="1" border="1" bgcolor="#ababab"><tr><td>
                        <table cellpadding="4" cellspacing="5" border="1" class="datatable" align="center">
                            <tr><th style="width: 25px;"></th>
                                <?php  // DATA HEADER
                                    foreach ($fielddef as $fkey=>$fld) {
                                        if ($fld[FLD_DISPLAY]) {
                                            $wd = isset($fld[FLD_DISPLAY_SZ]) ? " style=\"width: $fld[FLD_DISPLAY_SZ]ex\"" : "";
                                            if ($fld[FLD_DATABASE] == 'Car_Lane1') {
                                                $outputArray[1] = "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                                            }  else {
                                                if ($fld[FLD_DATABASE] == 'Car_Lane2') {
                                                    $outputArray[2] = "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                                                }  else {                                          
                                                    if ($fld[FLD_DATABASE] == 'Car_Lane3') {
                                                        $outputArray[3] = "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                                                    } else {
                                                        if ($fld[FLD_DATABASE] == 'Car_Lane4') {
                                                            $outputArray[4] = "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";
                                                        } else {
                                                            echo "<th$wd>" . htmlentities($fld[FLD_DISPLAY], ENT_QUOTES, $config['encoding']) . "</th>";                    
                                                        }
                                                    }
                                                }
                                            }



                                        }
                                }
                                if ($_SESSION['laneOrder'] == 'frontward') {
                                    echo $outputArray['1'];
                                    echo $outputArray['2'];
                                    echo $outputArray['3'];
                                    echo $outputArray['4'];
                                } else {
                                    echo $outputArray['4'];
                                    echo $outputArray['3'];
                                    echo $outputArray['2'];
                                    echo $outputArray['1'];
                                }                                
                            ?>
                        </tr>
                        <?php  // DATA
                            $checked = ' checked="checked"';
                            unset ($outputArray); 
                            $rowColor = "white";  
                            $i = 0;
                            foreach($rows as $row) {

                                if ($rowColor == "white") {
                                    $rowColor = "#C0C0C0";
                                } else {
                                    $rowColor = "white";
                                }
                                $bk = $i++ % 2 ? "" : ' class="sublight"';
                                echo "<tr$bk><td><input type='radio'$checked name='".RKEY."' value='".htmlentities($row[RKEY], ENT_QUOTES, $config['encoding'])."' /></td>";
                                foreach ($fielddef as $fkey=>$fld) {
                                    if ($fld[FLD_VISIBLE]) {

                                        $value =  htmlentities($row[$fkey], ENT_QUOTES, $config['encoding']);

                                        switch ($fld[FLD_DATABASE]) {
                                            case 'Heat_Header_idHeat_Header':
                                                // Show heat header (race template) name - not foriegn key
                                                $hhSql = "SELECT Description FROM heat_header where idHeat_Header =".$value;
                                                $hhResult = $dbconn->db_get_one_row($hhSql);
                                                $value = $hhResult['Description'];   
                                                break;

                                            case 'Car_Lane1':
                                                $outputArray['1'] = "<td align=center style=\"background-color:$rowColor\"><b>$value</b></td>";
                                                break;
                                            case 'Car_Lane2':
                                                $outputArray['2'] = "<td align=center style=\"background-color:$rowColor\"><b>$value</b></td>";
                                                break;
                                            case 'Car_Lane3':
                                                $outputArray['3'] = "<td align=center style=\"background-color:$rowColor\"><b>$value</b></td>";
                                                break;
                                            case 'Car_Lane4':
                                                $outputArray['4'] = "<td align=center style=\"background-color:$rowColor\"><b>$value</b></td>";
                                                break;
                                            default:

                                                if (!isset($value))
                                                    $value = "&nbsp;";
                                                echo "<td align=center style=\"background-color:$rowColor\"><b>$value</b></td>";
                                        }

                                    }
                                }
                                if ($_SESSION['laneOrder'] == 'frontward') {
                                    echo $outputArray['1'];
                                    echo $outputArray['2'];
                                    echo $outputArray['3'];
                                    echo $outputArray['4'];
                                } else {
                                    echo $outputArray['4'];
                                    echo $outputArray['3'];
                                    echo $outputArray['2'];
                                    echo $outputArray['1'];
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

    <form action="reverseLaneOrder.php" method="post">
        <input type="submit" name="formSubmit" value="reverse lane order" />
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
