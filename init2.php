<?php

    // Change log:
    // 2013-12-29: Rname function name pack55Inserts2013 -> pack55Inserts
    //2014-12-03: Update function signature for db_connect_only
    
    // PWD: ovrride the include path
    ini_set("include_path", "./includes"); 

    // PWD: Special include
    require_once('functions_#PWD.php'); 

    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";
    require_once "constants.inc.php";   

    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";





    //PWD: add logic here to force authentication if not done already - and move heading output to this spot
    session_start();
    $panel_heading = ucfirst("Purge and rebuild database");
    do_html_header($panel_heading);
    check_valid_user(); 
    doInit(); 


    /* Function doInit
    * Purpose: Perform the database initializations!
    * 
    */

    function doInit() {  
        global $config;   

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Update function signature
//      $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password']);
        $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password'], $config['dbname']);

        initializeSchema($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);
        initializeDatabase($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);                                                                                                                                                                                                                                                                       
        newDBInserts($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);
        newHeatInserts($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);      
        // pack55Inserts2013($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);      
        pack55Inserts($config['dbhost'], $config['db'], $config['dbname'], $config['admin_id'], $config['admin_password']);      
    }
?>



<!--PWD: Add "exit button -->
<form method="LINK" ACTION="menu.php">
    <input type="submit" value="  Exit   ">
</form>

<?php 

    // PWD: Output footer
    do_html_footer(); 

?>
