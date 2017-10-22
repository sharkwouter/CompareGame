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
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        $db = new Database("comparegames", "127.0.0.1", "3306", "root", "");
        
        $import = new Import();

        $games = $import->getGamesList();
        
        foreach ($games as $game){
            $db->addGame($game);
        }
        
        $db->printGames();
                

        ?>
    </body>
</html>
