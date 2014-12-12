<?php
$config['db'] = 'mysql';
$config['dbhost'] = 'localhost';
$config['dbport'] = 3306;
$config['dbname'] = 'atonllcc_pwd2009';
$config['dblogin'] = 'atonllcc_atonllc';
$config['dbpass'] = 'XW&@qW,i+&4P';
$config['encoding'] = 'utf-8';
$config['rows_per_page'] = 20;
$config['pager_items'] = 10;
$config['admin_id'] = "atonllcc_atonllc";
$config['admin_password'] = "XW&@qW,i+&4P";
$config['dictionary'] = 'words';
$config['autoRefreshON'] = 'YES';                        // Autorefresh of race results (YES = do auto-refresh)
$config['autoRefreshDuration'] = 15;                    // Intervals between autorefreshes
$config['autoRefreshVideoDuration'] = 15;               // Interval between race video autorefreshes
$config['autoRefreshHeatDisplayCount'] = 1;             // Number of times to heat results display in auto-refresh routine
$config['autoRefreshRacerPointsCount'] = 1;             // Number of times to show racer points display in auto-refresh routine
$config['autoRefreshHeatVideoClip'] = 1;                // Number of times to show heat video clip (remember, the clip will refresh on it's own)
$config['autoRefreshOnDeck'] = 1;                       // Number of times to show list of on-deck racers
$config['Page_Heading'] = 'Pack 55 Pinewood Derby Race';  // Heading on all pages                                                                                                                                                                                                                                                                                                      
$config['videoClipDirectory'] = 'http://atonllc.com/pwd2009/videoClips/';           // Directory to contain video clips
$config['videoClipDirectory2'] = '/home/atonllcc/public_html/pwd2009/videoClips/';           // Directory to contain video clips
$config['videoHeight'] = 560;                           // Hieght of video on web page
$config['videoWidth'] = 640;                            // Width of video on web page


// Race_heat_display properties
//** table #1
$config['rhd_t1_cellpadding'] = 1;
$config['rhd_t1_cellspacing'] = 0;
$config['rhd_t1_border'] = 0;
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
$config['rrd_t1_border'] = 0;
$config['rrd_t1_bgcolor'] = "#FF0000";

//** table #2
$config['rrd_t2_cellpadding'] = 0;
$config['rrd_t2_cellspacing'] = 2;
$config['rrd_t2_border'] = 1;
$config['rrd_t2_bgcolor'] = "#FF0000";

$config['rrdTableFontSize'] = 2;
?>
