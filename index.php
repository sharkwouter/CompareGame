<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    //Include classes
    include_once 'Game.php';
    include_once 'Database.php';
    include_once 'Parser.php';
    include_once 'Import.php';
    
    //Load config
    $configFile = "config.php";
    if(is_file($configFile)) {
        include_once $configFile;
    }
    
    //Create database object
    $db = new Database($dbname, $dbip, $dbport, $dbuser, $dbpass);
    
    //Update database if asked
    $update = filter_input(INPUT_POST, "update");
    if(isset($update)) {
        //Create import object, will trigger parsing every source
        $import = new Import();
        //Add all found games to the database
        $games = $import->getGamesList();
        foreach ($games as $game){
            $db->addGame($game);
        }
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form method="post"><input type="submit" name="update" value="Update Database"></form>
        
        <?php
            $db->printGames();
        ?>
    </body>
</html>
