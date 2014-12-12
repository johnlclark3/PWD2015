<?php

    // Modifications lof
    // 2013-12-29: Changed name of function to be consisten t so it does not have to change every year
    //  pack55Inserts2013 renamed pack55Inserts
    // 2014-12-03: Numwerous changes to support mysql -> mysqli changes
    // Note: due to volue, all mysqli_insert_id() changes were done without comments
    
    // JClark - changes noted below
    ini_set("include_path", "./includes"); 
    require_once('functions_database.php');
    require_once cleanID($_SERVER['DOCUMENT_ROOT']).".config.inc.php";     
    

    function db_connect() {
        // Jclark - added global $config variable and used elements in following SQL expression
        global $config; 
        $result = new mysqli($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname']);
        if (!$result) {
            throw new Exception('Could not connect to database server');
        } else {
            return $result;
        }
    }

    // Function: initializeDatabase
    // Purpose:  Create a new database from scratch - deleting the database first if it is there
    // Parameters:
    //   Database host system        - string
    //   Database type               - string
    //   New database schema name    - string
    //   Database admin ID           - string
    //   Database admin PW           - string



    function initializeDatabase($host, $database, $schema, $admin, $adminPassword){

        $dbconn = dbu_factory($database);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($host, $admin, $adminPassword);
        $dbconn->db_connect_only($host, $admin, $adminPassword, $database);

        // Attach to schema to add new database file...
        $sql = 'USE ' . $schema;
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Cannot use schema </br>"; 
            return $result;  
        } else {
            echo "Now using schema to add new database files </br>"; 
        }

        // Purge old tables - ORDER IS IMPORTANT!
        $sql = "DROP TABLE IF EXISTS race_heat_video;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table race_heat_video not dropped </br>"; 
        } else {
            echo "&#134  Table race_heat_video dropped; </br>"; 
        }        
        $sql = "DROP TABLE IF EXISTS race_heat;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table race_heat not dropped </br>"; 
        } else {
            echo "&#134  Table race_heat dropped; </br>"; 
        }        
        $sql = "DROP TABLE IF EXISTS heat;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table heat not dropped </br>"; 
        } else {
            echo "&#134  Table heat dropped; </br>"; 
        }

        // Race_registration 
        $sql = "DROP TABLE IF EXISTS raceregistration;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table race_registration not dropped </br>"; 
        } else {
            echo "&#134  Table race_registration dropped; </br>"; 
        }

        $sql = "DROP TABLE IF EXISTS racer;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table racer not dropped </br>"; 
        } else {
            echo "&#134  Table racer dropped; </br>"; 
        }
        $sql = "DROP TABLE IF EXISTS race;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table race not dropped </br>"; 
        } else {
            echo "&#134  Table race dropped; </br>"; 
        }
        $sql = "DROP TABLE IF EXISTS heat_header;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table heat_header not dropped </br>"; 
        } else {
            echo "&#134  Table heat_header dropped; </br>"; 
        }

        $sql = "DROP TABLE IF EXISTS user;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table user not dropped </br>"; 
        } else {
            echo "&#134  Table user dropped; </br>"; 
        }

        $sql = "DROP TABLE IF EXISTS den;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table den not dropped </br>"; 
        } else {
            echo "&#134  Table den dropped; </br>"; 
        }
        $sql = "DROP TABLE IF EXISTS valid_emails;";
        $result = $dbconn->db_query($sql);    
        if (!$result) {
            echo "Table valid_emails not dropped </br>"; 
        } else {
            echo "&#134  valid_emails dropped; </br>"; 
        }        

        // Create "valid_emails" table
        $sql = "CREATE TABLE valid_emails (
        IdEml INT NOT NULL AUTO_INCREMENT ,
        Eml_address VARCHAR(60) NOT NULL ,
        PRIMARY KEY (IdEml) );";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Valid_emails table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Valid_emails table created </br>"; 
        }

        // Create indexes over "valid_emails" table
        $sql = "CREATE UNIQUE INDEX eml_unique ON ". $schema .".valid_emails (Eml_address ASC) ;";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Index eml_unique not created </br>"; 
            return $result;  
        } else {
            echo "&#134   Index eml_unique created </br>"; 
        }  

        // Create "den" table
        $sql = "CREATE TABLE den (
        IdDen INT NOT NULL AUTO_INCREMENT ,
        Den_number INT NOT NULL ,
        Den_name VARCHAR(45) NOT NULL ,
        Den_leader VARCHAR(45) NOT NULL ,
        Den_level VARCHAR(45) NOT NULL ,
        Den_grade INT NOT NULL , 
        PRIMARY KEY (IdDen) );";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Den table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Den table created </br>"; 
        }


        // Create indexes over "den" table
        $sql = "CREATE UNIQUE INDEX den_unique ON ". $schema .".den (Den_number ASC) ;";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Index den_unique not created </br>"; 
            return $result;  
        } else {
            echo "&#134   Index den_unique created </br>"; 
        }


        // Create "racer" table
        
// JClark 2015-12-04 - Primary key cannot be NULL anymore        
//        $sql = "CREATE  TABLE racer (
//        idRacer INT NULL AUTO_INCREMENT ,
//        First_name VARCHAR(45) NULL ,
//        Last_name VARCHAR(45) NULL ,
//        Den_IdDen INT NULL ,
//        PRIMARY KEY (idRacer) ,
//        CONSTRAINT fk_racer_den
//        FOREIGN KEY (den_IdDen )
//        REFERENCES den (IdDen )
//        ON DELETE NO ACTION
//        ON UPDATE NO ACTION);";
        $sql = "CREATE  TABLE racer (
        idRacer INT AUTO_INCREMENT ,
        First_name VARCHAR(45) NULL ,
        Last_name VARCHAR(45) NULL ,
        Den_IdDen INT NULL ,
        PRIMARY KEY (idRacer) ,
        CONSTRAINT fk_racer_den
        FOREIGN KEY (den_IdDen )
        REFERENCES den (IdDen )
        ON DELETE NO ACTION
        ON UPDATE NO ACTION);";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Racer table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Racer table created </br>"; 
        }


        // Create indexes over "racer" table
        $sql = "CREATE INDEX by_name ON ". $schema .".racer (Last_name ASC, First_name ASC, idRacer ASC);";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Index by_name not created </br>"; 
            return $result;  
        } else {
            echo "&#134   Index by_name created </br>"; 
        }

        // Create "user" table
        $sql = "CREATE TABLE user (
        idUser INT NOT NULL AUTO_INCREMENT ,
        username varchar(16) ,
        passwd char(40) not null,
        admin char(1) not null,
        email varchar(100) not null,
        PRIMARY KEY (`idUser`)  
        );";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "User table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 User table created </br>"; 
        }


        // Create "heat header" table
        $sql = "CREATE  TABLE heat_header (
        idHeat_Header INT NOT NULL AUTO_INCREMENT ,
        Description VARCHAR(45) NULL ,
        Heats INT NULL ,
        Racers INT NULL ,
        Lanes INT NULL ,
        PRIMARY KEY (idHeat_Header) );";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Heat header table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Heat header table created </br>"; 
        }


        // Create "race" table
        $sql = "CREATE  TABLE race (
        idRace INT NOT NULL AUTO_INCREMENT ,
        Description VARCHAR(45) NOT NULL ,
        Race_status VARCHAR(15) NOT NULL ,
        Tracks INT NOT NULL,
        idHeat_Header INT NULL ,
        idRacer1 INT NULL ,
        idRacer2 INT NULL ,
        idRacer3 INT NULL ,
        Den_level VARCHAR(20) NOT NULL , 
        Race_prefix VARCHAR(3) NOT NULL , 
        PRIMARY KEY (idRace));";
    

        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Race table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Race table created </br>"; 
        }


        // Create "race_registration" table
        $sql = "CREATE  TABLE race_registration (
        idRace_registration INT NOT NULL AUTO_INCREMENT ,
        rrRace INT NOT NULL ,
        rrRacer INT NOT NULL ,
        rrCarNumber INT NOT NULL,
        rrPointsEarned INT NOT NULL ,
        rrPlace INT NULL ,

        PRIMARY KEY (idRace_registration) ,

        CONSTRAINT fk_Race
        FOREIGN KEY (rrRace)
        REFERENCES race (idRace)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,

        CONSTRAINT fk_idRacer
        FOREIGN KEY (rrRacer)
        REFERENCES racer (idRacer)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION);";

        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Race_registration table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Race_registration table created </br>"; 
        }
     

        // Create "heat" table
        $sql = "CREATE  TABLE heat (
        idHeat INT NOT NULL AUTO_INCREMENT ,
        Heat_Header_idHeat_Header INT NULL ,
        Number INT NOT NULL ,
        Car_Lane1 INT NULL ,
        Car_Lane2 INT NULL ,
        Car_Lane3 INT NULL ,
        Car_Lane4 INT NULL ,
        PRIMARY KEY (idHeat) ,
        CONSTRAINT fk_heat_heat_header
        FOREIGN KEY (heat_header_idHeat_header)
        REFERENCES heat_header (idHeat_Header)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION);";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Heat table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Heat table created </br>"; 
        }


        // Create "Race_heat" table
        $sql = "CREATE  TABLE race_heat (
        idRace_Heat INT NOT NULL AUTO_INCREMENT ,
        idRace INT NULL ,
        Number INT NULL ,
        Racer_lane1 INT NULL ,
        Racer_lane2 INT NULL ,
        Racer_lane3 INT NULL ,
        Racer_lane4 INT NULL ,
        Place_1 INT NULL ,
        Place_2 INT NULL ,
        Place_3 INT NULL ,
        Place_4 INT NULL ,
        PRIMARY KEY (idRace_Heat));";

        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Race heat table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Race heat table created </br>"; 
        }

        // Create "Race_heat_video" table
        $sql = "CREATE  TABLE race_heat_video (
        idRace_Heat_Video INT NOT NULL AUTO_INCREMENT ,
        RHVidRace_Heat INT NOT NULL ,
        Video_name VARCHAR(45) NOT NULL ,
        PRIMARY KEY (idRace_Heat_Video));";

        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Race heat video table not created </br>"; 
            return $result;  
        } else {
            echo "&#134 Race heat video table created </br>"; 
        }
    }

    function initializeSchema($host, $database, $schema, $admin, $adminPassword){

        $dbconn = dbu_factory($database);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($host, $admin, $adminPassword);
        $dbconn->db_connect_only($host, $admin, $adminPassword, $database);

        // Drop database if it exists
        // (do not stop on error - OK if it is not there)
        $sql = 'DROP DATABASE IF EXISTS ' . $schema;
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Schema " . $schema ." not dropped </br>"; 
        } else {
            echo "Schema " . $schema ." dropped </br>";    
        }

        // Add database from scratch
        $sql = 'CREATE DATABASE ' . $schema;
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Schema " . $schema . " not created successfully </br>"; 
            //      return $result;  
        }     else {
            echo "Schema " . $schema ." created successfully </br>"; 
        }
    }



    function newDBInserts($host, $database, $schema, $admin, $adminPassword){

        $dbconn = dbu_factory($database);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($host, $admin, $adminPassword);
//        $dbconn->db_connect_only($host, $admin, $adminPassword, $database);
        $dbconn->db_connect_only($host, $admin, $adminPassword, $schema);
        // Add in a valid email so that administration can begin
        
// JClark 2014-12-04 need to add ";" to end of insert statement
//      $sql = "INSERT INTO valid_emails values(0, 'jclark@atonllc.com')";
        $sql = "INSERT INTO valid_emails values(0, 'jclark@atonllc.com');";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Administrator email not added </br>"; 
        } else {
            echo "Administrator email added </br>";    
        }



        // Add in a valid admin user so that administration can begin
        $sql = "INSERT INTO user values(0, 'john', sha1('123123'), 'Y', 'jclark@atonllc.com')";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Administrator user not added </br>"; 
        } else {
            echo "Administrator user added </br>";    
        }
        $sql = "INSERT INTO user values(0, 'jimk', sha1('123123'), 'Y', '')";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Administrator user not added </br>"; 
        } else {
            echo "Administrator user added </br>";    
        }
        $sql = "INSERT INTO user values(0, 'jima', sha1('123123'), 'Y', '')";
        $result = $dbconn->db_query($sql);
        if (!$result) {
            echo "Administrator user not added </br>"; 
        } else {
            echo "Administrator user added </br>";    
        }
    }



    function newHeatInserts($host, $database, $schema, $admin, $adminPassword){

        $dbconn = dbu_factory($database);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($host, $admin, $adminPassword);
        $dbconn->db_connect_only($host, $admin, $adminPassword, $database);

        $sql = 'USE ' . $schema;
        $result = $dbconn->db_query($sql);

        // Assume that the heat and het header files are EMPTY!
        $sql = "INSERT INTO heat_header values(0, 'TD 04 Cars 4 Lanes Chart', 4, 4, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 4, 1, 2, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 4, 1, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 2, 3, 4, 1)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, '04 Cars 4 Lanes Perfect Chart', 4, 4, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);    
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 4, 3, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 1, 4, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 2, 1, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 3, 2, 1)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '05 Cars 4 Lanes Perfect Chart', 5, 5, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 1, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 2, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 1, 3, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 2, 4, 1)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 05 Cars 4 Lanes Chart', 5, 5, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 1, 2, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 4, 5, 1, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 3, 4, 5, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 2, 3, 4, 5)";            $result = $dbconn->db_query($sql); 



        $sql = "INSERT INTO heat_header values(0, '06 Cars 4 Lanes Partial-Perfect Chart', 6, 6, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 1, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 2, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 1, 3, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 2, 4, 1)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 06 Cars 4 Lanes Chart', 6, 6, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 2, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 6, 5, 4, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 3, 1, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 2, 4, 6, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 4, 3, 1, 6)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '07 Cars 4 Lanes Perfect Chart', 7, 7, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 7, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 1, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 2, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 1, 3, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 2, 4, 1)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 07 Cars 4 Lanes Chart', 7, 7, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 6, 5, 4, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 2, 4, 5, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 3, 7, 2, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 7, 3, 1, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 4, 1, 6, 2)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '08 Cars 4 Lanes Partial-Perfect Chart', 8, 8, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 7, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 8, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 1, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 2, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 1, 3, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 2, 4, 7)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 08 Cars 4 Lanes Chart', 8, 8, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 2, 1, 8, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 3, 4, 5, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 7, 5, 1, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 4, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 8, 3, 6, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 4, 7, 2, 5)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '09 Cars 4 Lanes Partial-Perfect Chart', 9, 9, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 7, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 8, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 9, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 1, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 2, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 1, 3, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 2, 4, 8)";            $result = $dbconn->db_query($sql); 




        $sql = "INSERT INTO heat_header values(0, 'TD 09 Cars 4 Lanes Chart', 9, 9, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 1, 2, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 7, 6, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 2, 4, 8, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 3, 5, 9, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 6, 8, 1, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 3, 4, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 7, 9, 5, 6)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '10 Cars 4 Lanes Partial-Perfect Chart', 10, 10, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 5, 7, 9, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 8, 10, 2, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 4, 6, 8, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 7, 9, 1, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 3, 5, 7, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 6, 8, 10, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 1, 3, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 2, 4, 9)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 10 Cars 4 Lanes Chart', 10, 10, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 1, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 2, 5, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 3, 8, 6, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 7, 1, 10, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 4, 3, 9, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 7, 2, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 6, 4, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 9, 8, 3)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '11 Cars 4 Lanes Partial-Perfect Chart', 11, 11, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 5, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 6, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 5, 7, 9, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 6, 8, 10, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 9, 11, 2, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 8, 10, 1, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 3, 5, 7, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 4, 6, 8, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 7, 9, 11, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 1, 3, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 2, 4, 10)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 11 Cars 4 Lanes Chart', 11, 11, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 2, 5, 4, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 3, 8, 6, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 7, 1, 10, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 4, 3, 9, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 11, 2, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 6, 4, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 9, 8, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 7, 1, 9)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '12 Cars 4 Lanes Partial-Perfect Chart', 12, 12, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 7, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 8, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 9, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 10, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 11, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 1, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 2, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 3, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 1, 5, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 2, 6, 11)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 12 Cars 4 Lanes Chart', 12, 12, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 12, 1, 10, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 8, 5, 2, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 3, 4, 9, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 11, 8, 1, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 10, 7, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 2, 3, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 6, 9, 5, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 4, 12, 8, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 7, 11, 6, 2)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, '13 Cars 4 Lanes Perfect Chart', 13, 13, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 7, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 8, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 9, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 10, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 11, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 12, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 13, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 1, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 2, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 3, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 13, 4, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 1, 5, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 13, 2, 6, 5)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 13 Cars 4 Lanes Chart', 13, 13, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 1, 10, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 8, 5, 2, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 3, 4, 9, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 11, 8, 1, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 10, 7, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 2, 13, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 6, 9, 5, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 4, 12, 8, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 7, 11, 13, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 12, 3, 6, 13)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '14 Cars 4 Lanes Partial-Perfect Chart', 14, 14, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 7, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 8, 10, 14, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 2, 4, 8, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 9, 11, 1, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 3, 5, 9, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 10, 12, 2, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 4, 6, 10, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 11, 13, 3, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 5, 7, 11, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 12, 14, 4, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 6, 8, 12, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 13, 1, 5, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 7, 9, 13, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 2, 6, 5)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 14 Cars 4 Lanes Chart', 14, 14, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 14, 10, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 8, 5, 2, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 3, 4, 13, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 12, 1, 9, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 11, 8, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 10, 7, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 2, 13, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 6, 9, 5, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 4, 12, 8, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 14, 11, 6, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 7, 3, 1, 13)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, '15 Cars 4 Lanes Partial-Perfect Chart', 15, 15, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 6, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 7, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 8, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 9, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 10, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 11, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 12, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 13, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 15, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 13, 1, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 14, 2, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 13, 15, 3, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 1, 4, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 15, 2, 5, 9)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 15 Cars 4 Lanes Chart', 15, 15, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 14, 15, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 8, 5, 2, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 3, 4, 13, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 12, 1, 9, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 11, 8, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 10, 15, 4, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 2, 13, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 15, 9, 5, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 4, 12, 8, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 14, 11, 6, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 7, 3, 1, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 6, 7, 10, 15)";            $result = $dbconn->db_query($sql); 




        $sql = "INSERT INTO heat_header values(0, '16 Cars 4 Lanes Partial-Perfect Chart', 16, 16, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 6, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 7, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 8, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 9, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 10, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 11, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 12, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 13, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 14, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 15, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 13, 16, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 14, 1, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 13, 15, 2, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 16, 3, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 15, 1, 4, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 16, 2, 5, 12)";            $result = $dbconn->db_query($sql); 

        $sql = "INSERT INTO heat_header values(0, 'TD 16 Cars 4 Lanes Chart', 16, 16, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 14, 15, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 12, 13, 2, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 16, 1, 6, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 8, 9, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 4, 5, 10, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 7, 12, 1, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 11, 16, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 15, 4, 9, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 3, 8, 13, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 10, 15, 8, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 3, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 2, 7, 16, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 6, 11, 4, 13)";            $result = $dbconn->db_query($sql); 




        $sql = "INSERT INTO heat_header values(0, '17 Cars 4 Lanes Partial-Perfect Chart', 17, 17, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 6, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 7, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 8, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 9, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 10, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 11, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 12, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 13, 17)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 14, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 15, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 13, 16, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 14, 17, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 13, 15, 1, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 16, 2, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 15, 17, 3, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 16, 1, 4, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 17, 17, 2, 5, 9)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 17 Cars 4 Lanes Chart', 17, 17, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 14, 15, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 17, 13, 2, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 16, 1, 6, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 8, 9, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 4, 5, 10, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 7, 12, 1, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 11, 16, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 15, 17, 9, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 3, 8, 13, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 10, 15, 17, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 3, 12, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 2, 7, 16, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 6, 11, 4, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 17, 12, 4, 8, 17)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, '18 Cars 4 Lanes Partial-Perfect Chart', 18, 18, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 3, 6, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 2, 4, 7, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 3, 5, 8, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 4, 6, 9, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 5, 7, 10, 17)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 6, 8, 11, 18)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 7, 9, 12, 1)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 10, 13, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 9, 11, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 10, 12, 15, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 11, 13, 16, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 12, 14, 17, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 13, 15, 18, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 14, 16, 1, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 15, 17, 2, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 16, 18, 3, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 17, 17, 1, 4, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 18, 18, 2, 5, 12)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 18 Cars 4 Lanes Chart', 18, 18, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);   
        $sql = "INSERT INTO heat values(0, $heatHeader, 1, 1, 2, 3, 4)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 2, 5, 6, 7, 8)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 3, 9, 10, 11, 12)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 4, 13, 14, 15, 16)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 5, 17, 1, 6, 11)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 6, 4, 5, 10, 15)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 7, 12, 18, 2, 7)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 8, 8, 9, 14, 3)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 9, 11, 8, 1, 13)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 10, 16, 12, 5, 2)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 11, 14, 15, 17, 18)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 12, 3, 16, 9, 6)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 13, 7, 4, 13, 10)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 14, 18, 11, 8, 5)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 15, 15, 3, 12, 17)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 16, 2, 13, 18, 9)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 17, 6, 7, 4, 14)";            $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader, 18, 10, 17, 16, 1)";            $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 19 Cars 4 Lanes Chart', 19, 19, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);  
        $sql = "INSERT INTO heat values(0, $heatHeader, 1,1,2,3,4)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,2,5,6,7,8)";                  $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,3,9,10,11,12)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,4,13,14,15,16)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,5,17,1,6,11)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,6,12,18,2,7)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,7,8,9,19,3)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,8,4,5,10,15)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,9,16,17,18,19)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,10,19,8,1,13)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,11,14,11,5,2)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,12,7,4,13,10)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,13,3,16,9,6)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,14,10,19,14,1)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,15,18,3,17,5)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,16,2,15,12,9)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,17,6,13,16,18)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,18,11,7,4,14)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,19,15,12,8,17)";              $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 20 Cars 4 Lanes Chart', 20, 20, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);  
        $sql = "INSERT INTO heat values(0, $heatHeader,1,1,2,3,4)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,2,5,6,7,8)";                  $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,3,9,10,11,12)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,4,13,14,15,16)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,5,17,18,19,20)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,6,16,1,6,11)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,7,12,17,2,7)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,8,8,13,18,3)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,9,4,9,14,19)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,10,20,5,10,15)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,11,19,8,1,10)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,12,11,20,13,2)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,13,3,12,5,14)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,14,15,4,17,6)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,15,7,16,9,18)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,16,14,7,20,1)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,17,2,15,8,9)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,18,10,3,16,17)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,19,18,11,4,5)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,20,6,19,12,13)";              $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 21 Cars 4 Lanes Chart', 21, 21, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);  
        $sql = "INSERT INTO heat values(0, $heatHeader,1,1,2,3,4)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,2,5,6,7,8)";                  $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,3,9,10,11,12)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,4,13,14,15,16)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,5,17,18,19,20)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,6,21,1,6,11)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,7,12,17,2,7)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,8,8,13,18,3)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,9,4,9,14,19)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,10,20,5,10,15)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,11,19,8,1,10)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,12,11,20,13,2)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,13,3,21,5,14)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,14,15,4,17,6)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,15,7,16,9,18)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,16,14,7,20,1)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,17,2,15,21,9)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,18,10,3,16,17)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,19,18,11,4,5)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,20,6,19,12,13)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,21,16,12,8,21)";              $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 22 Cars 4 Lanes Chart', 22, 22, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);  
        $sql = "INSERT INTO heat values(0, $heatHeader,1,1,2,3,4)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,2,5,6,7,8)";                  $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,3,9,10,11,12)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,4,13,14,15,16)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,5,17,18,19,20)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,6,21,1,6,11)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,7,12,22,2,7)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,8,8,13,18,3)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,9,4,9,14,19)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,10,20,5,10,15)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,11,16,17,21,22)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,12,11,8,1,14)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,13,7,4,17,10)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,14,3,20,13,6)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,15,19,16,9,2)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,16,15,12,5,18)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,17,6,21,16,13)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,18,22,19,12,1)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,19,18,11,4,5)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,20,14,7,20,9)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,21,2,15,8,21)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,22,10,3,22,17)";              $result = $dbconn->db_query($sql); 


        $sql = "INSERT INTO heat_header values(0, 'TD 23 Cars 4 Lanes Chart', 23, 23, 4)";
        $result = $dbconn->db_query($sql);
        $heatHeader = mysqli_insert_id($dbconn->fconn);  
        $sql = "INSERT INTO heat values(0, $heatHeader,1,1,2,3,4)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,2,5,6,7,8)";                  $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,3,9,10,11,12)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,4,13,14,15,16)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,5,17,18,19,20)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,6,21,1,6,11)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,7,12,22,2,7)";                 $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,8,8,13,23,3)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,9,4,9,14,19)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,10,20,5,10,15)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,11,16,17,21,22)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,12,23,8,1,14)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,13,7,4,17,10)";                $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,14,3,20,13,6)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,15,19,16,9,2)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,16,15,12,5,18)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,17,6,21,16,23)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,18,22,19,12,1)";               $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,19,18,11,4,5)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,20,14,7,20,9)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,21,2,15,8,21)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,22,10,3,22,17)";              $result = $dbconn->db_query($sql); 
        $sql = "INSERT INTO heat values(0, $heatHeader,23,11,23,18,13)";              $result = $dbconn->db_query($sql); 
    

    }


    // function pack55Inserts2013($host, $database, $schema, $admin, $adminPassword){
    function pack55Inserts($host, $database, $schema, $admin, $adminPassword){

        $dbconn = dbu_factory($database);
        $dbconn->db_extension_installed(); 
// JClark 2014-12-03 Change function signature
//      $dbconn->db_connect_only($host, $admin, $adminPassword);
        $dbconn->db_connect_only($host, $admin, $adminPassword, $schema);

        // Add den 1 racers
               
//        $result = $dbconn->db_query("INSERT INTO den values(0, 1, 'Webelos II Den 1 (Mrs Hayes)', 'Mrs Hayes', 'Webelos II', 5)");
//        $denNumber = mysqli_insert_id($dbconn->fconn);
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Jake', 'Clancy', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Bryan', 'Dawson', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Thomas', 'Hays', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Nathan', 'Jagh', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Leo', 'Maggiolino', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'James', 'Stamoulis', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Nicholas', 'Uzar', ".$denNumber.")");
//        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Colin', 'Wing', ".$denNumber.")");

        // Add den 2 racers 
        
        $result = $dbconn->db_query("INSERT INTO den values(0, 2, 'Wolf Den 2 (Mrs. Youssef)', 'Mrs. Youssef', 'Wolf', 2)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Dylan', 'Brown', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Brayden', 'D'Ambrosio', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Justin', 'DiVitto', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Friedrich', 'Kueter ', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Grady', 'Martinek', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Landin', 'Youssef ', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Cary', 'Zhang ', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Eoghan', 'Grant', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Ryan', 'Cavanaugh', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Brendan', 'Staunton', ".$denNumber.")");
        
        // Add den 3 racers 

        $result = $dbconn->db_query("INSERT INTO den values(0, 3, 'Tiger Den 3 (M Conte & M Mastromarchi)', 'M Conte & M Mastromarchi', 'Tiger', 1)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Alexander', 'Nealon', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'William', 'Nealon', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Tristan', 'Conte', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Andrew', 'Mastromarchi', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Eshan', 'Nalk', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Connor', 'Elben', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Cameron', 'Citro', ".$denNumber.")");

        // Add den 5 racers 
        
        $result = $dbconn->db_query("INSERT INTO den values(0, 5, 'Webelos I Den 5 (Mr Nascimento)', 'Mr Nascimento', 'Webelos I', 4)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'James', 'Ares', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Mark', 'Cayer', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Ryan', 'Grandpre', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Cameron', 'Kane', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Alexander', 'Nascimento', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Michael', 'Nunez-Mercedes', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Marcus', 'Reeve-Patel', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Kieran', 'Reeve-Patel', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Matthew', 'MacNeil', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Gennaro', 'Orrino', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Peter', 'Wixted', ".$denNumber.")");

        // Add den 6 racers  
        
        $result = $dbconn->db_query("INSERT INTO den values(0, 6, 'Webelos II Den 6 (Mrs Hetherington)', 'Mrs Hetherington', 'Webelos II', 5)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Ryan', 'DiVitto', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Benjamin', 'Davison', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Joshua', 'Drake', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Owen', 'Garron', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Michael', 'Nunez-Mercedes', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Jack', 'Quinlan', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Brian', 'Rabideau', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Aiden', 'Richardson', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Jakob', 'Zhang', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Connor', 'Grant', ".$denNumber.")");

        // Add den 8 racers 
        
        $result = $dbconn->db_query("INSERT INTO den values(0, 8, 'Webelos I Den 8 (Mr Colebourn)', 'Mr Colebourn', 'Webelos I', 4)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Yusuf', 'Ali', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Kyle', 'Colebourn', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Alexander', 'Reineke', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Arnav', 'Thube', ".$denNumber.")");
   
        // Add den 10 racers   
        
        $result = $dbconn->db_query("INSERT INTO den values(0, 10, 'Bear Den 10 (Mr Jorgensen)', 'Mr Jorgensen', 'Bear', 3)");
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Mustafa', 'Ahmed', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Joshua', 'Colebourn', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Andrew', 'Eiben', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'John', 'Feeney', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Landen', 'Jorgensen', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Perrin', 'LoRusso', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Harrison', 'Prohaska', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Thomas', 'Rabideau', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Dillon', 'Richardson', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Michael', 'Cashel', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Vincent', 'Orrino', ".$denNumber.")");
        $result = $dbconn->db_query("INSERT INTO racer values(0, 'Michael', 'Cashel', ".$denNumber.")");
   
        $sql = "INSERT INTO den values(0, 12, 'Siblings', '', 'Sibling', 6)";
        $result = $dbconn->db_query($sql);  
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $sql = "INSERT INTO racer values(0, 'Andrew', 'Roberts', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Annabella', 'Orrino', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Amaury', 'Henroz', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Owen', 'Puslifer', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Shay', 'Pulsifer', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Mark', 'Stamoulis', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Louis', 'Stamoulis', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Ethan', 'MacGillivray', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Ben', 'MacGillivray', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Justin', 'MacGillivray', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Megan', 'Tucker', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Ashley', 'Costello', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Haley', 'Costello', $denNumber)";
        $result = $dbconn->db_query($sql);


        $sql = "INSERT INTO den values(0, 13, 'Adult', '', 'Adult', 7)";
        $result = $dbconn->db_query($sql);  
        $denNumber = mysqli_insert_id($dbconn->fconn); 
        $sql = "INSERT INTO racer values(0, 'Gary', 'Hays', $denNumber)";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO racer values(0, 'Doug', 'Tucker', $denNumber)";
        $result = $dbconn->db_query($sql);

        // Add email addresses to get started (jclark@atonbllc.com already added)
        $sql = "INSERT INTO valid_emails values(0, 'tlhowe@charter.net')";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO valid_emails values(0, 'dougjt@yahoo.com')";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO valid_emails values(0, 'johnlclark3@gmail.com')";
        $result = $dbconn->db_query($sql);
        $sql = "INSERT INTO valid_emails values(0, 'David.MacNeil@BuroHappold.com')";
        $result = $dbconn->db_query($sql);
    }



    function getDenArray(){

        global $config;

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
        $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);    

        // Get ALL den record

        $sql = "SELECT * FROM den";
        $result = $dbconn->db_get_all_rows($sql);
        if (!$result) {
            echo "falure DEN1</br>"; 
        } else {
            foreach($result as $key => $value) {
                $denArray[$value[IdDen]] = $value[Den_name];
            }

            return $denArray; 
        }


    }

    /* Function: recomputeRacerScores
    *  Purpose: Re-calculate the individual racer scores for a race
    * 
    * 
    */

    function recomputeRacerScores($race){

        global $config;

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
        $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);    

        // Zero out lead racrers iu race file
        $sql = "UPDATE race SET idRacer1=0, idRacer2=0, idRacer3=0 where idRace = ".$race;
        $result = $dbconn->db_query($sql);
        // Set all racer scores to zero in racer_registration file
        $sql = "UPDATE race_registration SET rrPointsEarned = 0 where rrRace=".$race;
        $result = $dbconn->db_query($sql);
        if ($result) {

            // Get ALL race_heat records - results entered for heats
            $sql = "SELECT * FROM race_heat where idRace = ".$race;
            $result = $dbconn->db_get_all_rows($sql);
            if (!$result) {
                // echo "falure RECOMPUTE1</br>";  COMMENTED OUT - WILL FAIL TILL RACE STARTS
            } else {
                foreach($result as $key => $value) {
                    // Go get race_registration record for 1st place racer and add 4 points to race score
// JClark 2014-12-11 - correct syntax in next line
//                  if ($value[Place_1] <> 0) {
                    if ($value['Place_1'] <> 0) {
                        addToRacerScore($race, $value['Place_1'], 4);
                        addToRacerScore($race, $value['Place_2'], 3);
                        addToRacerScore($race, $value['Place_3'], 2);
                        addToRacerScore($race, $value['Place_4'], 1);
                    }
                }
            }

            // Zero out lead racrers iu race file
            $sql = "UPDATE race SET idRacer1=0, idRacer2=0, idRacer3=0 where idRace = ".$race;
            $result = $dbconn->db_query($sql); 

            // Get ALL race_registration records again - now that they have most up-to-date scores
            $sql = "SELECT rrPointsEarned, idRace_registration, rrRace, rrRacer FROM race_registration where (rrRace=".$race.") and (rrPointsEarned > 0) ORDER BY rrPointsEarned DESC, rrCarNumber";
            $assignPlace = $dbconn->db_get_all_rows($sql);
            if (!$assignPlace) {
                // echo "falure RECOMPUTE2</br>";        COMMENTED OUT - WILL FAIL TILL RACE IS STARTED...
            } else {
                $ap = 0;
                foreach($assignPlace as $key => $value) {
                    // For each car in the race:
                    // - Assign it a "place"; if same score as last car - give it the same "place"
                    $ap++;
                    if ($ap == 1) {
                        $place = 1;
                    } else {
                        if ($value['rrPointsEarned'] < $lastPoints)
                            $place++;
                    }
                    $lastPoints = $value['rrPointsEarned'];                    
                    $sql = "UPDATE race_registration SET rrPlace=".$place." where idRace_registration=".$value['idRace_registration'];
                    $result = $dbconn->db_query($sql);

                    switch ($ap) {
                        case 1:
                            $firstPlace =  $value['rrRacer']; 
                            break;   
                        case 2:
                            $secondPlace =  $value['rrRacer'];   
                            break;    
                        case 3:
                            $thirdPlace =  $value['rrRacer'];   
                            $sql = "UPDATE race SET idRacer1=".$firstPlace.", idRacer2=".$secondPlace.", idRacer3=".$thirdPlace." where idRace = ".$value['rrRace'];
                            $result = $dbconn->db_query($sql);        
                            break;    
                        default:
                            break;
                    }
                }
            }
        }
    }




    function addToRacerScore($race, $racer, $points){

        global $config;

        $dbconn = dbu_factory($config['db']);
        $dbconn->db_extension_installed(); 
        $dbconn->db_connect($config['dbhost'], $config['dblogin'], $config['dbpass'], $config['dbname'], $config['dbport']);    

        $return = true;
        // Update racer_registration to add the points passed in
        $sql = "UPDATE race_registration SET rrPointsEarned = (rrPointsEarned+".$points.") where ((rrRace=".$race.") and (rrRacer=".$racer."))";
        $result = $dbconn->db_query($sql);
        //if ($results) {

        // Get ALL race_heat records - results entered for heats
        //$sql = "SELECT * FROM race_heat where idRace = ".$race;
        //$result = $dbconn->db_get_all_rows($sql);
        //if (!$result) {
        //    $return = false;
        //}   
        //}
        //return $result;  
    }


    /* 
    * Function = dirList
    * Purpose = dump a list of all files in directory
    * Input parameter = Directory name
    * Return value = array of file names (or directory names)
    * 
    */

    function dirList ($directory) 
    {

        // create an array to hold directory list
        $results = array();

        // create a handler for the directory
        $handler = opendir($directory);

        // Initialize loop flag
        $go = true;

        // Never enter loop if no data to deal with....
        $file = readdir($handler);
        if (!$file) $go = false; 

        // keep going until all files in directory have been read
        while ($go == true) {

            // if $file isn't this directory or its parent, 
            // add it to the results array

            if ($file != '.' && $file != '..')
                $results[] = $file;

            // get next file and set flag if we shoudl quit
            $file = readdir($handler);
            if (!$file) $go = false; 
        }

        // tidy up: close the handler
        closedir($handler);

        // done!
        return $results;

    }

?>
