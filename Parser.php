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
    private $productsList; //This one is for parsing individual products from the page
    
    //These are for XML parsing
    private $html;
    private $path;
    //Temp solution platforms
    private $acceptedPlaforms = array("Xbox One");

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

        //This is fucking retarded, but I expect every game to have it's own id
        foreach ($this->path->query($QueryProducts) as $product) {
            $this->productsList[] = $product->attributes->getNamedItem('id')->value;
        }
        if (in_array($platform, $this->acceptedPlaforms)) {
            $this->platform = $platform;
            $this->Parse();
        } else {
            echo $platform."is not a supported console";
        }
    }

    public function Parse() {
        foreach ($this->productsList as $product) {
            //Create arry which is used for creating the current game object
            $result = array();
            
            //Values which we already know
            $result["platform"] = $this->platform;
            $result["store"] = $this->store;
            
            //for every game on the current page:
            foreach ($this->queryList as $key => $query) {
                
                //insert product id into query
                $query = str_replace("%product%", $product, $query);
                foreach ($this->path->query($query) as $value) {
                    $output = $value;
                }
                
                if($key == "link"){
                    $result [$key] = $output->attributes->getNamedItem('href')->value;
                } else {
                    $result [$key] = $output->nodeValue;
                }
            }
            $this->addGame(new Game($result["name"], $result["price"], $result["platform"], $result["store"], $result["link"]));
        }
    }

    public function getGamesList() {
        return $this->gamesList;
    }

    public function getStore() {
        return $this->store;
    }

    public function addGame(Game $add) {
        $this->gamesList [] = $add;
    }

}
