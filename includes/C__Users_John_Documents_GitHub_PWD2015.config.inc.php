<?php
$config['db'] = 'mysql';
$config['dbhost'] = 'localhost';
$config['dbport'] = 3306;
$config['dbname'] = '2015pwd';
$config['dblogin'] = 'root';
$config['dbpass'] = 'pwd2015';
$config['encoding'] = 'utf-8';
$config['rows_per_page'] = 20;
$config['pager_items'] = 10;
$config['admin_id'] = "root";
$config['admin_password'] = "pwd2015";
$config['dictionary'] = 'words';
$config['autoRefreshON'] = 'YES';                        // Autorefresh of race results (YES = do auto-refresh)
$config['autoRefreshDuration'] = 5;                    // Intervals between autorefreshes
$config['autoRefreshVideoDuration'] = 15;               // Interval between race video autorefreshes
$config['autoRefreshHeatDisplayCount'] = 3;             // Number of times to heat results display in auto-refresh routine
$config['autoRefreshRacerPointsCount'] = 2;             // Number of times to show racer points display in auto-refresh routine
$config['autoRefreshHeatVideoClip'] = 1;                // Number of times to show heat video clip (remember, the clip will refresh on it's own)')
$config['autoRefreshOnDeck'] = 3;                       // Number of times to show list of on-deck racers
$config['Page_Heading'] = 'Pack 55 & 58 Pinewood Derby Race';  // Heading on all pages    
$config['videoClipDirectory'] = 'videoClips\\';           // Directory to contain video clips
$config['videoClipDirectory2'] = 'videoClips\\';           // Directory to contain video clips
$config['videoHeight'] = 560;                           // Hieght of video on web page
$config['videoWidth'] = 640;                            // Width of video on web page

// Race_heat_display properties
//** table #1
$config['rhd_t1_cellpadding'] = 1;
$config['rhd_t1_cellspacing'] = 0;
$config['rhd_t1_border'] = 1;
$config['rhd_t1_bgcolor'] = "#FF0000";

//** table #2
$config['rhd_t2_cellpadding'] = 0;
$config['rhd_t2_cellspacing'] = 2;
$config['rhd_t2_border'] = 1;
$config['rhd_t2_bgcolor'] = "#FF0000";

$config['rhdTableFontSize'] = 2;

// Race_registration_display properties
//** table #1
$config['rrd_t1_cellpadding'] = 1;
$config['rrd_t1_cellspacing'] = 0;
$config['rrd_t1_border'] = 1;
$config['rrd_t1_bgcolor'] = "#FF0000";

//** table #2
$config['rrd_t2_cellpadding'] = 0;
$config['rrd_t2_cellspacing'] = 2;
$config['rrd_t2_border'] = 1;
$config['rrd_t2_bgcolor'] = "#FF0000";

$config['rrdTableFontSize'] = 2;

?>
