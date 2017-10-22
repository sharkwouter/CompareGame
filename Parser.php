<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'Game.php';

/**
 * Description of Parser
 *
 * @author wouter
 */
class Parser {

    //These will become strings   
    private $store;
    private $url;
    private $platform;
    //These will become booleans
    private $hasPages;
    //These will become arrays
    private $gamesList = array();
    private $queryList = array();
    //Products
    private $productsList;
    //These are for XML parsing
    private $html;
    private $path;
    //Temp solution platforms
    private $acceptedPlaforms = array("Xbox One","Gamecube");

    public function __construct(string $store, string $platform, string $url, bool $hasPages, string $QueryProducts, string $QueryName, string $QueryPrice, string $QueryLink) {
        //Load given variables
        $this->store = $store;
        $this->url = $url;
        $this->hasPages = $hasPages;

        //Create the queries list
        $this->queryList = array(
            "name" => $QueryName,
            "price" => $QueryPrice,
            "link" => $QueryLink
        );

        //Create objects required for XML parsing, assuming nothing goes wrong
        $this->html = new DOMDocument();
        $this->html->loadHTMLFile($this->url);
        $this->path = new DOMXPath($this->html);

        //Load only what is relevant
        $products = $this->path->query($QueryProducts);

        //Fill up the productsList with DomXPath objects which contain only 1 product
        foreach ($products as $product) {
            $loadHtml = $this->html->saveHTML($product);
            $dom = new DOMDocument();
            $dom->loadHTML($loadHtml);
            $this->productsList [] = new DOMXPath($dom);
        }


        if (in_array($platform, $this->acceptedPlaforms)) {
            $this->platform = $platform;
            $this->Parse();
        } else {
            echo $platform . " is not a supported console";
        }
    }

    //Generates the game list from the html page
    public function Parse() {
        foreach ($this->productsList as $product) {
            //Create arry which is used for creating the current game object
            $result = array();

            //Values which we already know
            $result["platform"] = $this->platform;
            $result["store"] = $this->store;

            //for every game on the current page:
            foreach ($this->queryList as $key => $query) {

                foreach ($product->query($query) as $value) {
                    $output = $value;
                }

                //Do different things with the output, based on which query we used
                if ($key == "link") {
                    $result [$key] = $output->getAttribute('href');
                } elseif ($key == "price") {
                    $result [$key] = $this->Getfloat($output->nodeValue);
                } else {
                    $result [$key] = $output->nodeValue;
                }
            }

            //Don't add games with no price to the gamesList
            if ($result ["price"] > 0) {
                $new = new Game($result["name"], $result["price"], $result["platform"], $result["store"], $result["link"]);
                if (!$this->isDuplicateGame($new)) {
                    $this->addGame($new);
                } else {
                    break;
                }
            }
        }
    }

    public function getGamesList() {
        return $this->gamesList;
    }

    public function getStore() {
        return $this->store;
    }

    private function addGame(Game $add) {
        //Only add games which aren't in our list already
        if($this->isDuplicateGame($add) == FALSE) {
            $this->gamesList [] = $add;
        }
    }

    //Use the equals function of a game to see if it is already in the gamesList, so we don't add games twice
    private function isDuplicateGame(Game $new) {
        foreach ($this->gamesList as $game) {
            if ($game->equals($new)) {
                return true;
            }
        }
        return false;
    }

    //Thanks anonymous user on php.net
    private function Getfloat($str) {
        if (strstr($str, ",")) {
            $str = str_replace(".", "", $str); // replace dots (thousand seps) with blancs
            $str = str_replace(",", ".", $str); // replace ',' with '.'
        }

        if (preg_match("#([0-9\.]+)#", $str, $match)) { // search for number that may contain '.'
            return floatval($match[0]);
        } else {
            return floatval($str); // take some last chances with floatval
        }
    }
}
