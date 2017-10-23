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
if (is_file($configFile)) {
    include_once $configFile;
}

//Create database object
$db = new Database($dbname, $dbip, $dbport, $dbuser, $dbpass);

//Update database if asked
//    $updateButton = filter_input(INPUT_POST, "update");
//    if(isset($updateButton)) {
//        //Create import object, will trigger parsing every source
//        $import = new Import();
//        //Add all found games to the database
//        $games = $import->getGamesList();
//        foreach ($games as $game){
//            $db->addGame($game);
//        }
//    }

//Get what has been searched, but only if the search button has been pressed
$searchString = filter_input(INPUT_GET, "search");
if(empty($searchString)){
    $searchString = "";
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
        <?= $navbar->printNavbar() ?>
        <div>
            <form method="get">
                Search: 
                <input type="text" name="search" value="<?= $searchString ?>" />
                <input type="submit" value="Search" />
            </form>
        </div>
        <?php
        //Show a set of games depending on if the search was used
        $db->searchGames($searchString);
        ?>
    </body>
</html>
