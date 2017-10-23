<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    //Include classes
    include_once 'classes/Url.php';
    include_once 'classes/Game.php';
    include_once 'classes/Database.php';
    include_once 'classes/Parser.php';
    include_once 'classes/Import.php';
    include_once 'classes/Navbar.php';
    
    //Load config
    $configFile = "config.php";
    if(is_file($configFile)) {
        include_once $configFile;
    }
    
    //Create database object
    $db = new Database($dbname, $dbip, $dbport, $dbuser, $dbpass);

    //Create navbar object
    $navbar = new Navbar();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?=$navbar->printNavbar()?>
    </body>
</html>
