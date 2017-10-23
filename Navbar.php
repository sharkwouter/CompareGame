<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Navbar
 *
 * @author wouter
 */
class Navbar {
    //List with items
    private $items = array("Home" => "index.php", "Manage" => "manage.php", "Update" => "update.php");

    public function printNavbar(){
        echo "<nav>\n|";
        foreach ($this->items as $name => $link) {
            echo "\t<a href='".$link."'>".$name."</a>\t|";
        }
        echo "\n</nav>\n";
    }
}
