<?php

include_once 'classes/Parser.php';
include_once 'classes/Database.php';

//This class makes the import happen for all stores
class Import {
    //Database object from the constructor
    private $db;

    public function __construct(Database $db) {
        //Get access to the database
        $this->db = $db;
    }

    //This updates the games in the database for the current store and platform
    public function update(int $storeid, int $platformid) {
        //Get the data to give to the parser from the database
        $data = $this->db->getParseData($storeid, $platformid);

        //Parse the page
        if (!empty($data)) {
            $parser = new Parser($data["company"], $data["platform"], new URL($data["url"]), $data["product"], $data["name"], $data["price"], $data["link"], $data["nextpage"]);

            //Add the games to the database
            $this->addGames($parser->getGamesList());

            //Notify user that we tried something, there is no error check yet
            print("<div class='message'><b>The database has been updated</b></div>");
        } else {
            print("<div class='message'><b>The requested website/store combination doesn't exist</b></div>");
        }
    }

    //Add all games in the gamesList to the database
    private function addGames($gameList) {
        foreach ($gameList as $game) {
            $this->db->addGame($game);
        }
    }

}
