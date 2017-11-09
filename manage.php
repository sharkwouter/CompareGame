<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';
include_once 'classes/Navbar.php';
include_once 'classes/Store.php';

//Create navbar object
$navbar = new Navbar();

//Get all the stores
$stores = $GLOBALS["db"]->getStores();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <h2>Manage Stores</h2>
        <h3>Add Stores</h3>
        <form>
            <table>
                <tr><td>Name</td><td><input type="text" name="name" value="" /></td></tr>
                <tr><td>Link</td><td><input type="text" name="link" value="" /></td></tr>
                <tr><td></td><td><input type="submit" value="Add" /></td></tr>
            </table>
        </form>
        
        <h3>Manage stores</h3>
        <table>
            <tr><th>Store</th><th>Remove</th></tr>
            <?php
                foreach($stores as $store){
                    print("<tr><td><a href='".$store->getUrl()."'>".$store."</a></td><td><form><input type='submit' name='".$store."' value='Remove' /></form></td></tr>\n");
                }
            ?>
        </table>
    </body>
</html>
