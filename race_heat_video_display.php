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

    // Find out if there is a video for the most recently completed heat for race in progress
    $showVideo = 'NO';
    $sql = "SELECT max(Number) FROM race_heat where (idRace = ".$_SESSION['raceToEnterResults'].") and (Place_1 <> 0)";
    $lastHeatResult = $dbconn->db_get_one_row($sql);
    if ($lastHeatResult['max(Number)'] <> NULL)     {
        $temp = $lastHeatResult['max(Number)'];
        $sql = "SELECT idRace_Heat FROM race_heat where (idRace = ".$_SESSION['raceToEnterResults'].") and (Number = ".$temp.")";
        $lastHeatResult = $dbconn->db_get_one_row($sql);
        if ($lastHeatResult) {
            // There IS a "last heat run" - now see if there is a video for it
            $sql = "SELECT Video_name FROM race_heat_video where RHVidRace_Heat = ".$lastHeatResult['idRace_Heat'];
            $lastHeatVideo = $dbconn->db_get_one_row($sql);
            if (($lastHeatVideo) and ($lastHeatVideo['Video_name'] > '')) {
                $showVideo = 'YES';
                $video = $config['videoClipDirectory'].$lastHeatVideo['Video_name'];
                $videoHeight = $config['videoHeight']; 
                $videoWidth = $config['videoWidth']; 
                $lastHeat = $lastHeatResult['max(Number)'];
            }
        }
    }
    // Look to see if auto-refresh should be on.. if so...
    if ($config['autoRefreshON'] == 'YES') {

        // Incremenent the count for number of displays made
        // - if time to return to display heats, go to that display now
        // (if no video to display AND autorefresh is ON... move on to that next display NOW)
        $_SESSION['autoRefreshControl#3'] = $_SESSION['autoRefreshControl#3'] + 1;
        if (($_SESSION['autoRefreshControl#3'] > $config['autoRefreshHeatVideoClip']) 
        or ($showVideo == 'NO')) {
            $_SESSION['autoRefreshControl#3'] = 0;
            echo '<meta http-equiv="refresh" content="0; URL=table_race_heat_display.php">';   
        }         else {


            // Get description for race identified by session variable $_SESSION['raceToEnterResults']
            $sql = "SELECT idRace, idHeat_Header, Description, idRacer1, idRacer2, idRacer3 FROM race where idRace = ".$_SESSION['raceToEnterResults'];
            $resultRACE = $dbconn->db_get_one_row($sql);
            $raceDescription = $resultRACE['Description']; 

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
            $panel_heading = ("Race: ".$raceDescription."  (Heat #".$lastHeat." of ".$resultHH['Heats']." heats)");         
            if ($config['autoRefreshON'] == 'YES')  {
                do_html_header_refresh($panel_heading, $config['autoRefreshVideoDuration']);
            } else    {
                do_html_header($panel_heading);  
            }

        ?>


        </head>
        <body>
            <?php
                if ($showVideo == 'YES') {
                ?>

                <object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" width="<?php echo $videoWidth; ?>" height="<?php echo $videoHeight; ?>" codebase="http://www.microsoft.com/Windows/MediaPlayer/">
                    <param name="Filename" value="<?php echo $video; ?>">
                    <param name="AutoStart" value="true">
                    <param name="ShowControls" value="false">
                    <param name="BufferingTime" value="2">
                    <param name="ShowStatusBar" value="false">
                    <param name="AutoSize" value="true">
                    <param name="InvokeURLs" value="false">
                    <param name="PlayCount" value="25">  
                    <!-- <embed src="http://walkernewsdownload.googlepages.com/HP-iPaq-614.wmv" type="application/x-mplayer2" autostart="1" enabled="1" showstatusbar="1" showdisplay="1" showcontrols="1" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,0,0" width="480" height="360"></embed> -->
                    <!-- <embed src="<?php echo $video; ?>" type="application/x-mplayer2"  autostart="1" PlayCount="25" enabled="1" showstatusbar="0" showdisplay="1" showcontrols="0" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,0,0" width="<?php echo $videoWidth; ?>" height="<?php echo $videoHeight; ?>"></embed> -->
                </object>
            </body>
            </html>

            <?php
            } else {      
            ?>
            <p>Sorry - there is no video for the most recently completed heat.  Try again later.</p>
            </body>
            </html>
            <?php         
            }
        }
    }

?>
