<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'classes/Url.php';
require_once 'classes/Store.php';
require_once 'classes/Game.php';

/**
 * Description of Parser
 *
 * @author wouter
 */
class Parser {

    //These will become strings   
    private $store;
    private $platform;
    private $products; //Contains the products query
    private $urlBase;
    //These will become booleans
    private $hasPages = false;
    //Counter for duplicates
    private $duplicatesFound = 0;
    private $maxDuplicates = 1;
    //These will be used for XML
    private $html;
    private $path;
    //These will become arrays
    private $gamesList = array();
    private $queryList = array();
    //Products
    private $productsList;

    //Constructor for single page websites, probably not used much
    public function __construct(string $store, string $platform, Url $url, string $QueryProducts, string $QueryName, string $QueryPrice, string $QueryLink, string $QueryNextPage) {
        //Load given variables
        $this->store = $store;
        $this->platform = $platform;
        $this->products = $QueryProducts;
        $this->urlBase = $url->getBase();

        //Create the queries list
        $this->queryList = array(
            "name" => $QueryName,
            "price" => $QueryPrice,
            "link" => $QueryLink,
        );
        
        //We use the nextpage variable so we can parse more pages
        $nextpage = $url;
        
        //Parse the given page, quit when there is no next page or we found a duplicate
        while(!empty($nextpage) && $this->duplicatesFound < $this->maxDuplicates) {
            $this->parsePage($nextpage);
            if(empty($QueryNextPage)){
                $nextpage = "";
            } else {
                $nextpage = $this->getNextURL($QueryNextPage);
            }
        }
    }
    
    //Generates the game list from the html page
    public function parsePage($url) {
        //Create objects required for XML parsing, assuming nothing goes wrong
        $this->html = new DOMDocument();
        $this->html->loadHTMLFile($url);
        $this->path = new DOMXPath($this->html);
        
        foreach ($this->getProductList($this->products) as $product) {
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

            //Don't add duplicate games or games with no price to the gamesList
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

    //Get a list of XPaths which contain individual products
    private function getProductList(string $query) {
        $productList = array();

        //Load only what is relevant
        $products = $this->path->query($query);        

        //Fill up the productsList with DomXPath objects which contain only 1 product
        foreach ($products as $product) {
            $loadHtml = $this->html->saveHTML($product);
            $dom = new DOMDocument();
            $dom->loadHTML($loadHtml);
            $productList [] = new DOMXPath($dom);
        }
        return $productList;
    }
    
    private function getNextURL(string $query){
        $output = "";
        //The foreach because sometimes the next page is the last one in a list of links
        foreach ($this->path->query($query) as $value) {
           $output = $value->getAttribute('href');
        }
        if(isset($output) && !strstr($output, $this->urlBase)) {
            $output = $this->urlBase."/".$output;
        }
        return $output;
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
                $this->duplicatesFound += 1;
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
