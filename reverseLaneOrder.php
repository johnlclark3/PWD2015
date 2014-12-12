<?php

    // Function: reverseLaneOrder.php
    // Purpose: Handle press of button to change order of lanes in heat maintenance 
    session_start();

    // Be sure to assign a value to the session varaible if it is not assigned already
    if (!isset($_SESSION['laneOrder'])) {
        $_SESSION['laneOrder'] = 'frontward';
    }
    // Toggle the session value indicating the order the lanes should be presented    
      if ($_SESSION['laneOrder'] == 'frontward')  {
       $_SESSION['laneOrder'] = 'backward';
      } else {
            $_SESSION['laneOrder'] = 'frontward';
      }

    // redisplay the heat maintenance
    echo '<meta http-equiv="refresh" content="0; URL=table_heat.php">'; 
?>
