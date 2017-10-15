<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    include_once 'Game.php';
    include_once 'Parser.php';
    include_once 'Import.php';
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        
        //Game class test
        //$gametest = new Game("Testname", 5.99, "Xbox One", "Nedgame","https://nedgame.nl/");
        //echo $gametest->returnSQL("Test");
        
        $import = new Import();
        
        $games = $import->getGamesList();
        
        foreach ($games as $game){
            $game->printData();
        }
                

        ?>
    </body>
</html>
