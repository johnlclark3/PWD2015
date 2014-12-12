

<?php

// JClark 2014-12-03 Updated to reflect new siggnature for db_connect_only function


    /**
    * Initialize_Database: completely rebuild MySQL database 
    * 
    * @param string schema
    * 
    */
    function initialize_Database($schema){

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
    // JClark 2014-12-03 Update method signature
    //      $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password']);
        $dbconn->db_connect_only($config['dbhost'], $config['admin_id'], $config['admin_password'], $config['dbname']);

        // Drop database if it exists
        $sql = 'DROP DATABASE IF EXISTS ' . $schema;
        $result = $dbconn->db_query($sql);

        // Add database from scratch
        $sql = 'CREATE DATABASE ' . $schema;
        $result = $dbconn->db_query($sql);
        if (!$result) {
            throw new Exception('Could not connect to database server');
        } else {
            return $result;
        }
    }
?>
