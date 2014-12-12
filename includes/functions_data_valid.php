<?php


    // JClark 2014-12-03 Update function signature for db_connect_only
    
    //ini_set("include_path", "./includes"); 
    //require_once('functions_database.php');
    //require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";   

    ini_set("include_path", "./includes"); 
    require_once('functions_#PWD.php'); 
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";
    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";
    require_once "constants.inc.php";    

    function filled_out($form_vars) {
        // test that each variable has a value
        foreach ($form_vars as $key => $value) {
            if ((!isset($key)) || ($value == '')) {
                return false;
            }
        }
        return true;
    }

    function valid_email($address) {
        // check an email address is possibly valid
        if (ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address)) {
            return true;
        } else {
            return false;
        }
    }


    function cleanID($ID) {
        // create server unique ID by getting "document root" and replacing bad characters
        // replace "\", "/" and ";" with underscores
        $ID = str_replace("\\","_",$ID);
        $ID = str_replace("/","_",$ID);  
        $ID = str_replace(":","_",$ID);
        return $ID; 

    }

    function validateRace($heatHeader, $racerCount) {   
        global $config;       

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password']);
        $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password'], $config['dbname']);

        $index = 0;
        $msg = '';
        while ((++$index <= $racerCount) and ($msg == '')) {       

            // set flags for lane count
            $lane1 = 0;
            $lane2 = 0;
            $lane3 = 0;
            $lane4 = 0;

            // get all heat detail records for the heat header
            $sqlHeats = "SELECT * FROM heat WHERE Heat_Header_idHeat_Header = $heatHeader";   
            $resultHeats = $dbconn->db_get_all_rows($sqlHeats);
            foreach($resultHeats as $key => $value)  {

                if ($value['Car_Lane1'] == $index) {
                    $lane1++;
                }
                if ($value['Car_Lane2'] == $index) {
                    $lane2++;
                }
                if ($value['Car_Lane3'] == $index) {
                    $lane3++;
                }
                if ($value['Car_Lane4'] == $index) {
                    $lane4++;
                }

                // Look for the same car specified twice
                if (($value['Car_Lane1'] == $value['Car_Lane2']) or
                ($value['Car_Lane1'] == $value['Car_Lane3']) or
                ($value['Car_Lane1'] == $value['Car_Lane4']) or
                ($value['Car_Lane2'] == $value['Car_Lane3']) or
                ($value['Car_Lane2'] == $value['Car_Lane4']) or  
                ($value['Car_Lane3'] == $value['Car_Lane4']))  {
                    $t = $value['Number'];               
                    $msg = "Car found more than once in heat# $t.";
                }
                // 

            }

            // If error already located, show it
            if ($msg <> '') {
                errorHandlerHH($msg); 
            } else {
                // else, indicate if any car is in a lane more or less than one time
                if ($lane1 <> 1) {
                    $msg = "Car $index found $lane1 times in lane #1.  Should be found once.";
                    errorHandlerHH($msg);
                } else {
                    if ($lane2 <> 1) {
                        $msg = "Car $index found $lane2 times in lane #2.  Should be found once.";
                        errorHandlerHH($msg);
                    }
                    else {
                        if ($lane3 <> 1) {
                            $msg = "Car $index found $lane3 times in lane #3.  Should be found once.";
                            errorHandlerHH($msg);

                        }  else {
                            if ($lane4 <> 1) {
                                $msg = "Car $index found $lane4 times in lane #4.  Should be found once.";
                                errorHandlerHH($msg);

                            }
                        }
                    }
                }
            }
        }

        if ($msg == '') {
            $msg = "Race validated OK!";
            errorHandlerHH($msg);
        }
    }

    function errorHandlerHH($errorMessage) {    
    ?>     
    <body>
        <h5><?php echo ($errorMessage); ?></h5>
    </body>   
    <form method="LINK" ACTION="<?php "table_".$table.".php"?>">    
        <input type="submit" value="Go back">
    </form>

    <?php  
    }

?>
