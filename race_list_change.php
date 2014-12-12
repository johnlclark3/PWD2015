<?php

    // Function: race_list_change.php
    // Purpose: Handle press of button to change selection
    // on table_race display of main table

    session_start();

 

    // Examine the checkboxes that were selected and assign thier value
    // to the session varibals

    if(isset($_POST['listTiger']) && $_POST['listTiger'] == 'T')  {
        $_SESSION['listTiger'] = 'checked';
    } else {
        $_SESSION['listTiger'] = 'unchecked';
    }

    if(isset($_POST['listWolf']) && $_POST['listWolf'] == 'W')  {
        $_SESSION['listWolf'] = 'checked';
    } else {
        $_SESSION['listWolf'] = 'unchecked';
    }

    if(isset($_POST['listBear']) && $_POST['listBear'] == 'B')  {
        $_SESSION['listBear'] = 'checked';
    } else {
        $_SESSION['listBear'] = 'unchecked';
    }

    if(isset($_POST['listW1']) && $_POST['listW1'] == 'W1')  {
        $_SESSION['listW1'] = 'checked';
    } else {
        $_SESSION['listW1'] = 'unchecked';
    }

    if(isset($_POST['listW2']) && $_POST['listW2'] == 'W2')  {
        $_SESSION['listW2'] = 'checked';
    } else {
        $_SESSION['listW2'] = 'unchecked';
    }

    if(isset($_POST['listSibling']) && $_POST['listSibling'] == 'S')  {
        $_SESSION['listSibling'] = 'checked';
    } else {
        $_SESSION['listSibling'] = 'unchecked';
    }

    if(isset($_POST['listAdult']) && $_POST['listAdult'] == 'A')  {
        $_SESSION['listAdult'] = 'checked';
    } else {
        $_SESSION['listAdult'] = 'unchecked';
    }

       // Examine the checkboxes - if all were unchecked - then set them ALL back to checked
    if ((!isset($_POST['listTiger'])) && (!isset($_POST['listWolf'])) && (!isset($_POST['listBear'])) &&
    (!isset($_POST['listW1'])) && (!isset($_POST['listW2'])) && (!isset($_POST['listSibling'])) &&
    (!isset($_POST['listAdult']))) {

        $_SESSION['listTiger']      = 'checked'; 
        $_SESSION['listWolf']       = 'checked'; 
        $_SESSION['listBear']       = 'checked'; 
        $_SESSION['listW1']         = 'checked'; 
        $_SESSION['listW2']         = 'checked'; 
        $_SESSION['listSibling']    = 'checked'; 
        $_SESSION['listAdult']      = 'checked'; 
    }

    
    // redisplay the race list
    echo '<meta http-equiv="refresh" content="0; URL=table_race.php">'; 
?>
