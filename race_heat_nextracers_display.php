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
    check_valid_user();        

    $dbconn = dbu_factory($config['db']);
    $dbconn->db_extension_installed();
    $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);
    // Get description for race identified by session variable $_SESSION['raceToEnterResults']
    $sql = "SELECT idRace, idHeat_Header, Description, idRacer1, idRacer2, idRacer3, Race_prefix FROM race where idRace = ".$_SESSION['raceToEnterResults'];
    $resultRACE = $dbconn->db_get_one_row($sql);
    $raceDescription = $resultRACE['Description']; 
    $racePrefix = $resultRACE['Race_prefix'];   
    // Get the next heat run for this race
    $nextHeat = NULL;
    $sql = "SELECT min(Number) FROM race_heat where (idRace = ".$_SESSION['raceToEnterResults'].") and (Place_1 = 0)";
    $nextHeatResult = $dbconn->db_get_one_row($sql);
    if ($nextHeatResult['min(Number)'] <> NULL) {
        $nextHeat = $nextHeatResult['min(Number)']; 
    }
    $sql = "SELECT idRace_Heat, Racer_lane1, Racer_lane2, Racer_lane3, Racer_lane4 FROM race_heat where (idRace = ".$_SESSION['raceToEnterResults'].") and (Number = ".$nextHeat.")";
    $nextHeatResult = $dbconn->db_get_one_row($sql);

    // Look to see if auto-refresh should be on.. if so...
    if ($config['autoRefreshON'] == 'YES') {
        // Incremenent the count for number of displays made
        // - if time to return to next screen, go to that display now
        $_SESSION['autoRefreshControl#4'] = $_SESSION['autoRefreshControl#4'] + 1;
        if ($_SESSION['autoRefreshControl#4'] > $config['autoRefreshOnDeck'])   {
            $_SESSION['autoRefreshControl#4'] = 0;
            echo '<meta http-equiv="refresh" content="0; URL=race_heat_video_display.php">';   
        }
    }
    // Get description for race identified by session variable $_SESSION['raceToEnterResults']
    $sql = "SELECT idRace, idHeat_Header, Description, idRacer1, idRacer2, idRacer3, Race_prefix FROM race where idRace = ".$_SESSION['raceToEnterResults'];
    $resultRACE = $dbconn->db_get_one_row($sql);
    $raceDescription = $resultRACE['Description']; 
    // Get names and car numbers of the next racers 
    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$nextHeatResult['Racer_lane1'];
    $result = $dbconn->db_get_one_row($sql);
    $sql = "SELECT rrCarNumber FROM race_registration where (rrRacer = ".$nextHeatResult['Racer_lane1'].") and (rrRace = ".$_SESSION['raceToEnterResults'].")";
    $resultRR = $dbconn->db_get_one_row($sql);
    $lane1 = $result['First_name']." ".$result['Last_name']."&nbsp;&nbsp;(Car: ".$resultRACE['Race_prefix'].$resultRR['rrCarNumber'].")";

    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$nextHeatResult['Racer_lane2'];
    $result = $dbconn->db_get_one_row($sql);
    $sql = "SELECT rrCarNumber FROM race_registration where (rrRacer = ".$nextHeatResult['Racer_lane2'].") and (rrRace = ".$_SESSION['raceToEnterResults'].")";
    $resultRR = $dbconn->db_get_one_row($sql);
    $lane2 = $result['First_name']." ".$result['Last_name']."&nbsp;&nbsp;(Car: ".$resultRACE['Race_prefix'].$resultRR['rrCarNumber'].")";

    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$nextHeatResult['Racer_lane3'];
    $result = $dbconn->db_get_one_row($sql);
    $sql = "SELECT rrCarNumber FROM race_registration where (rrRacer = ".$nextHeatResult['Racer_lane3'].") and (rrRace = ".$_SESSION['raceToEnterResults'].")";
    $resultRR = $dbconn->db_get_one_row($sql);
    $lane3 = $result['First_name']." ".$result['Last_name']."&nbsp;&nbsp;(Car: ".$resultRACE['Race_prefix'].$resultRR['rrCarNumber'].")";

    $sql = "SELECT First_name, Last_name FROM racer where idRacer = ".$nextHeatResult['Racer_lane4'];
    $result = $dbconn->db_get_one_row($sql);
    $sql = "SELECT rrCarNumber FROM race_registration where (rrRacer = ".$nextHeatResult['Racer_lane4'].") and (rrRace = ".$_SESSION['raceToEnterResults'].")";
    $resultRR = $dbconn->db_get_one_row($sql);
    $lane4 = $result['First_name']." ".$result['Last_name']."&nbsp;&nbsp;(Car: ".$resultRACE['Race_prefix'].$resultRR['rrCarNumber'].")";
    // Get number of heats from heat_header
    $sqlHH = "SELECT Heats FROM heat_header where idHeat_Header=".$resultRACE['idHeat_Header'];
    $resultHH = $dbconn->db_get_one_row($sqlHH);
    $totalHeats = $resultHH['Heats'];
    // Output panel heading (force refresh if turned on)
    $panel_heading = ("Race: ".$raceDescription);         
    if ($config['autoRefreshON'] == 'YES')  {
        do_html_header_refresh($panel_heading, $config['autoRefreshDuration']);
    } else    {
        do_html_header($panel_heading);  
    }

    if ($nextHeat <> NULL) { 
        echo "<h2><u>On-Deck Racers for heat #$nextHeat of $totalHeats </u></br></br></br>";
        echo "</h3>";
        echo "<h1>";
        echo "&nbsp;&nbsp;Lane #1: &nbsp;&nbsp;$lane1</b></br></br>";
        echo "&nbsp;&nbsp;Lane #2: &nbsp;&nbsp;$lane2</br></br>";           
        echo "&nbsp;&nbsp;Lane #3: &nbsp;&nbsp;$lane3</br></br>";       
        echo "&nbsp;&nbsp;Lane #4: &nbsp;&nbsp;$lane4</br></br>";     
        echo "</h1></head></html>";  
    }
    else {
        echo "<h2>Please stand by for next race to start</br>";
        echo "</h1></head></html>";  
    }
?>



       
