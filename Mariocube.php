<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mariocube
 *
 * @author wouter
 */
class Mariocube {

    //put your code here
    private $file = "mariocube.html";

    function __construct() {
        $xml = new DOMDocument();
        $xml->loadHTMLFile($this->file);

        $tables = $xml->getElementsByTagName('a');
        $x = $tables->item(2);
        foreach ($tables AS $item) {
            print $xml->saveHTML($item) . "<br><br>";
        }
    }

}
