<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    //Include classes
    include_once 'Url.php';
    include_once 'Game.php';
    include_once 'Database.php';
    include_once 'Parser.php';
    include_once 'Import.php';
    include_once 'Navbar.php';
    
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
