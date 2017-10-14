<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
    include_once 'Game.php';
    include_once 'Parser.php'
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
        
        $nedgame = new Parser("nedgame", "nedgame.html", false,
                "//table[@class='productTable']/tbody/tr",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='title']/div[@class='titlewrapper']/a/h3",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='title']/div[@class='titlewrapper']/span[@class='productinfo']/a");
        
        $games = $nedgame->getGameList();
        
        foreach ($games as $game){
            $game->printData();
        }
                

        ?>
    </body>
</html>
