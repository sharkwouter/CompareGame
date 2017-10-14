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
class Nedgame {

    //put your code here
    private $origin = "https://www.nedgame.nl/xbox-one/games/pagina_1/";
    private $file = "nedgame.html";
    private $html;
    private $path;

    function __construct() {

        //load page for parsing
        $this->html = new DOMDocument();
        try {
            $this->html->loadHTMLFile($this->file);
        } catch (Exception $e) {
            
        }

        //XPath, so we can do queries against it
        $this->path = new DOMXPath($this->html);

        //The queries we will use with what they should return


        $productquery = "//table[@class='productTable']/tbody/tr";
        foreach ($this->path->query($productquery) as $product) {
            $productList[] = $product->attributes->getNamedItem('id')->value;
        }
        foreach ($productList as $productid) {

            $queries = array(
                "Title" => "//table[@class='productTable']/tbody/tr[@id='".$productid."']/td[@class='title']/div[@class='titlewrapper']/a/h3",
                "Price" => "//table[@class='productTable']/tbody/tr[@id='".$productid."']/td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']",
            );

            foreach ($queries as $key => $query) {
                $entries = $this->path->query($query);
                foreach ($entries as $value) {
                    print "<p>" . $key . ": " . $value->nodeValue . "</p>";
                }
            }
        }
    }

    function getInfo($productid) {
        $queries = array(
            "Title" => "//table[@class='productTable']/tbody/tr/td[@class='title']/div[@class='titlewrapper']/a/h3",
            "Price" => "//table[@class='productTable']/tbody/tr/td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']",
        );

        foreach ($queries as $key => $query) {
            $entries = $path->query($query);
            print "<p>" . $key . ": " . $entries->nodeValue . "</p>";
            
//            foreach ($entries as $value) {
//                print "<p>" . $key . ": " . $value->nodeValue . "</p>";
//            }
        }
    }

}
