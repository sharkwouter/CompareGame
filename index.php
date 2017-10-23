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
    
    //Update database if asked
    $updateButton = filter_input(INPUT_POST, "update");
    if(isset($updateButton)) {
        //Create import object, will trigger parsing every source
        $import = new Import();
        //Add all found games to the database
        $games = $import->getGamesList();
        foreach ($games as $game){
            $db->addGame($game);
        }
    }
    
    //Get what has been searched, but only if the search button has been pressed
    $searchString = "";
    $submitButton = filter_input(INPUT_POST, "submit");
    if(isset($submitButton)) {
        $searchString = filter_input(INPUT_POST, "search");
    }
    
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
        <form method="post">
            Search: 
            <input type="text" name="search" value="<?=$searchString?>" />
            <input type="submit" name="submit" value="Search" />
            <input type="submit" value="Clear Search" />
            <input type="submit" name="update" value="Update Database" />
        </form>
        <?php
        //Show a set of games depending on if the search was used
        if(empty($searchString)) {
            $db->printGames(0);
        } else {
            $db->searchGames($searchString);
        }
        ?>
    </body>
</html>
