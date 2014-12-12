<?php
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


?>


<script  type="text/javascript">

    <!--
    function confirmation() {
        var answer = confirm("Are you sure you want to completely clear the database?")
        if (answer){
            window.location = "init2.php"; 
        }
        else{
            alert("Database not nuked.")
        }
    }

    //-->

</script>
</head>
<body>
<input type="button" value="Nuke database?" onclick="confirmation()" />&nbsp;  


<!--PWD: Add "exit button -->
<form method="LINK" ACTION="menu.php">
    <input type="submit" value="  Exit   ">
</form>

<?php 

    // PWD: Output footer
    do_html_footer(); 

?>
