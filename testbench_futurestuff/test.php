<?php


    // PWD: ovrride the include path
    ini_set("include_path", "./includes"); 

    // PWD: Special include
    require_once('functions_#PWD.php'); 
    
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";

    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";


              $race = 1;
              $racer = 4;
              $points = 7;
              
              recomputeRacerScores($race);
              // addToRacerScore($race, $racer, $points);                                                                                                                                                                                                                                                            
       
                 
?>
