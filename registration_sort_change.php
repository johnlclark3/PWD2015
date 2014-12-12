<?php

    // Function: registration_sort_change.php
    // Purpose: Handle press of button to change sort criteria on
    // on table_race_registration display of main table

    session_start();

 

    // Examine the radio button pressed and assign values
    // to the session variabls

    if ($_POST['sortOption'] == 'car')     {
        $_SESSION['RegSortCar'] = 'checked';
        $_SESSION['RegSortPlace'] = 'unchecked';
    } else {
        $_SESSION['RegSortCar'] = 'unchecked';
        $_SESSION['RegSortPlace'] = 'checked';
    }
    
    // redisplay the race registration screen
    echo '<meta http-equiv="refresh" content="0; URL=table_race_registration.php">'; 
?>
