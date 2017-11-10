<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';
require_once 'classes/Navbar.php';

//Create navbar object
$navbar = new Navbar();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Platforms</title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <article>
            
        </article>
    </body>
</html>
