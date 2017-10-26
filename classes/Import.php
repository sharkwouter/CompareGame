<?php

include_once 'classes/Parser.php';
include_once 'classes/Database.php';

//This class makes the import happen for all stores
class Import {
    private $gameList = array();
    
    //Database object from the constructor
    private $db;
    
    public function __construct(Database $db) {
        //Get access to the database
        $this->db = $db;
    }
    
    //This updates the games in the database for the current store and platform
    public function update(int $storeid, int $platformid){
        //Get the data to give to the parser from the database
        $data = $this->db->getParseData($storeid,$platformid);
        //Parse the page
        $parser = new Parser($data["company"], $data["platform"], new URL($data["url"]), $data["product"], $data["name"], $data["price"], $data["link"], $data["nextpage"]);
        
        //Create our gameslist
        $this->gameList = $parser->getGamesList();
        
        //Add the games to the database
        $this->updateDatabase();
    }
    
    //Add all games in the gamesList to the database
    private function updateDatabase() {
        foreach($this->gameList as $game){
            $this->db->addGame($game);
        }
    }
}
