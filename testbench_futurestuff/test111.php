<?php
  
  echo $_SERVER['DOCUMENT_ROOT']; 
    $output = str_replace("\\","_",$_SERVER['DOCUMENT_ROOT']);
        $output = str_replace(":","_",$output);
    echo $output; 

?>
