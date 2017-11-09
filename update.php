<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';
include_once 'classes/Url.php';
include_once 'classes/Game.php';
include_once 'classes/ParseDataObject.php';
include_once 'classes/Parser.php';
include_once 'classes/Import.php';
include_once 'classes/Navbar.php';

//Create navbar object
$navbar = new Navbar();

//Import object for interracting with database
$import = new Import($GLOBALS['db']);

//Update if shit is set
$storeid = filter_input(INPUT_POST, "storeid");
$platformid = filter_input(INPUT_POST, "platformid");
if (!empty($storeid) && !empty($platformid)) {
    $import->update($storeid, $platformid);
}

//Get ParseDataObjects
$parseDataObjects = $GLOBALS['db']->getParseDataObjects();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php $navbar->printNavbar(); ?>
        <table>
            <?php
            //Print table header
            $fields = array("store", "platform", "url", "run","last update");
            print("<tr>");
            foreach ($fields as $field) {
                print("<th>" . $field . "</th>");
            }
            print("</tr>\n");
            
            //Print table rows
            foreach ($parseDataObjects as $parseDataObject) {
                $parse = $parseDataObject->getData();
                print("<tr><td>" . $parse["store"] . "</td><td>" . $parse["platform"] . "</td><td><a href='" . $parse["url"] . "'>link</a></td>\n");

                //Create run button
                print("<td><form method='post'>");
                print("<input type='hidden' name='storeid' value='" . $parse["storeid"] . "' />");
                print("<input type='hidden' name='platformid' value='" . $parse["platformid"] . "' />");
                print("<input type='submit' value='Run' />");
                print("</form></td>\n");
                
                print("<td>".$parseDataObject->getLastUpdate()."</td>");
                
                //close table row
                print("</tr>");
            }
            ?>
        </table>
    </body>
</html>
