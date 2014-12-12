<?php

    // Function: removeRaceHeatVideo
    // Purpose: Remove a specific record and redirect to race_heat page
    // Note: the video clip itself is not deleted - only the database record

    ini_set("include_path", "./includes"); 
    require_once('functions_#PWD.php');
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php"; 
    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";
    require_once "constants.inc.php";  

    session_start();
    check_valid_user(); 


    // Use race_heat in session variable to identify and remove race_heat_video record
    $conn = db_connect();
    $result = $conn->query("DELETE from race_heat_video WHERE RHVidRace_Heat =".$_SESSION['raceHeatForVideo']);

    // redisplay the race registration screen
    echo '<meta http-equiv="refresh" content="0; URL=table_race_heat.php">'; 
?>
