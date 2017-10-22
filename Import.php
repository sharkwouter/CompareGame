<?php

//This class makes the import happen for all stores
class Import {
    //put your code here
    private $storeList = array();
    
    public function __construct() {
        //Here we create a Parser object for every store, this may take a while
 
        //Nedgame, Xbox One
        $this->storeList [] = new Parser("nedgame", //Store name in lowercase
                "Xbox One", //The platform, has to be in the supported platforms list
                "nedgame.html", //filename or url of store page
                "//table[@class='productTable']/tbody/tr", //Tag containing individual products
                "//td[@class='title']/div[@class='titlewrapper']/a/h3", //product name
                "//td[@class='buy']/div[@class='koopdiv']/div[@class='currentprice']", //price
                "//td[@class='title']/div[@class='titlewrapper']/a", //link
                "" //The query used to go to the next page
                );

        //Tweakers, Xbox One
        $this->storeList [] = new Parser("tweakers", //Store name in lowercase
                "Xbox One", //The platform, has to be in the supported platforms list
                "tweakers.html", //filename or url of store page
                "//tr[@class='largethumb']", //Tag containing individual products
                "//a[@class='editionName']", //product name
                "//p[@class='price']/a", //price
                "//a[@class='editionName']", //link
                "" //The query used to go to the next page
                );
    
        //Mariocube, Gamecube
        $this->storeList [] = new Parser("mariocube", //Store name in lowercase
                "Gamecube", //The platform, has to be in the supported platforms list
                "mariocube.html", //filename or url of store page
                "//div[@id='main_midden']/div[@id='winkelblokl']|//div[@id='main_midden']/div[@id='winkelblokr']", //Tag containing individual products
                "//div[@id='wtitel']/a|//div[@id='wtitels']/a", //product name
                "//div[@id='wprijs']", //price
                "//div[@id='wtitel']/a|//div[@id='wtitels']/a", //link
                "//center//div[@id='kopje']/a" //The query used to go to the next page
                );

        
    }
    
    public function getGamesList(){
        $gamesList = array();
        foreach ($this->storeList as $store) {
            $gamesList = array_merge($gamesList, $store->getGamesList());
        }
        return $gamesList;
    }
    
    function uploadData(){
        
    }
}
