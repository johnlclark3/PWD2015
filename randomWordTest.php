<?php

    ini_set("include_path", "./includes");  

    require_once('functions_#PWD.php');

    echo "Dictionary = ".$config['dictionary'];
    echo "</br>"   ;
    
    echo get_random_word(6, 13); 
?>
