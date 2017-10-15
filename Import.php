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
                "//table[@class='productTable']/tbody/tr", //Tag containing individual products
                "//td[@class='title']/div[@class='titlewrapper']/a/h3", //product name
                "//td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']", //price
                "//td[@class='title']/div[@class='titlewrapper']/a" //link
                );

        //Tweakers
        $this->storeList [] = new Parser("tweakers", "Xbox One",
                "tweakers.html", false,
                "//tr[@class='largethumb']", //Tag containing individual products
                "//a[@class='editionName']", //product name
                "//p[@class='price']/a", //price
                "//a[@class='editionName']" //link
                );
    
    }
    
    public function getGamesList(){
        $gamesList = array();
        foreach ($this->storeList as $store) {
            $gamesList = array_merge($gamesList, $store->getGamesList());
        }
        return $gamesList;
    }
}
