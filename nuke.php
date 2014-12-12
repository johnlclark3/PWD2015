<?php

    // This php functionis identical to init2.php - except there is no authentication
    // There are no links to this function... when it is called - it just hammers the database... 
    
    // Change log:
    // 2013-12-29: rename function pack55Inserts2013 -> pack55Inserts
    // JClark 2014-12-03 Updated to reflect new siggnature for db_connect_only function

    ini_set("include_path", "./includes"); 
    require_once('functions_#PWD.php'); 
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";
    require_once "constants.inc.php";   
    require_once "db_utils.inc";
    require_once "db_". $config['db'] . ".inc";

    doInit(); 

    function doInit() {  
        global $config;   

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change method signature
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
