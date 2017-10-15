<?php

//This class makes the import happen for all stores
class Import {
    //put your code here
    private $storeList = array();
    
    public function __construct() {
        //Here we create a Parser object for every store, this may take a while
 
        //Nedgame
        $this->storeList [] = new Parser("nedgame", "Xbox One",
                "nedgame.html", false,
                "//table[@class='productTable']/tbody/tr",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='title']/div[@class='titlewrapper']/a/h3",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']",
                "//table[@class='productTable']/tbody/tr[@id='%product%']/td[@class='title']/div[@class='titlewrapper']/a");

    }
    
    public function getGamesList(){
        $gamesList = array();
        foreach ($this->storeList as $store) {
            $gamesList = array_merge($gamesList, $store->getGamesList());
        }
        return $gamesList;
    }
}
