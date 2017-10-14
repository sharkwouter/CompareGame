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
    //These will become booleans
    private $hasPages;
    //These will become arrays
    private $gamesList;
    private $queryList;
    private $productsList; //This one is for parsing individual products from the page
    
    //These are for XML parsing
    private $html;
    private $path;

    public function __construct(string $store, string $url, bool $hasPages, string $QueryProducts, string $QueryName, string $QueryPrice, string $QueryPlatform) {
        //Load given variables
        $this->store = $store;
        $this->url = $url;
        $this->hasPages = $hasPages;

        //Create the queries list
        $this->queryList = array(
            "name" => $QueryName,
            "price" => $QueryPrice,
            "platform" => $QueryPlatform
        );

        //Create objects required for XML parsing, assuming nothing goes wrong
        $this->html = new DOMDocument();
        $this->html->loadHTMLFile($this->url);
        $this->path = new DOMXPath($this->html);

        //This is fucking retarded, but I expect every game to have it's own id
        foreach ($this->path->query($QueryProducts) as $product) {
            $this->productsList[] = $product->attributes->getNamedItem('id')->value;
        }
        
        $this->Parse();
    }

    public function Parse() {
        foreach($this->productsList as $product) {
            /* @var $result array */
            $result;
            foreach ($this->queryList as $key => $query) {
                //insert product id into query
                $query = str_replace("%product%", $product, $query);
                
                foreach ($this->path->query($query) as $value) {
                    $result [$key] = $value->nodeValue;
                }
            }
            $this->addGame(new Game($result["name"], $result["price"], $result["platform"], $this->store, $this->url));
        }
    }

    public function getGameList() {
        return $this->gamesList;
    }

    public function getStore() {
        return $this->store;
    }

    public function addGame(Game $add) {
        $this->gamesList [] = $add;
    }

}
