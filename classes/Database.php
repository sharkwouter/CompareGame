<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author wouter
 */
class Database {

    //Database
    private $db;

    public function __construct(array $config) {
        //Connect to the database if we actually got config data
        if (!empty($config)) {
            try {
                $this->db = new PDO("mysql:host=" . $config["ip"] . ";dbname=" . $config["database"] . ";port=" . $config["port"], $config["username"], $config["password"]);
                $this->connected = true;
            } catch (PDOException $ex) {
                $this->printError("the connection to the database could not be established");
            }
        } else {
            $this->printError("no config data received");
        }
    }

    //Print the database error and exit the program! There is no use in continueing without database
    private function printError(string $error) {
        print("<p>An issue has been found: " . $error . "</p>");
        exit();
    }

    //Add a game to the database
    public function addGame(Game $game) {
        //Get data from game object
        $data = $game->returnData();

        //Boolean for checking if the game is already in the database
        $exists = false;

        //TODO: Don't update all existing games
        //run query to see if the game is already in the database
        $queryFindGame = $this->db->prepare("SELECT link FROM Game WHERE link=?");
        $queryFindGame->execute(array($data["link"]));

        //If we find one, sert exists to true
        while ($game = $queryFindGame->fetch()) {
            $exists = true;
            break;
        }

        //Either add or update the database entry based on if it exists already
        if ($exists) {
            $queryAdd = $this->db->prepare("UPDATE Game SET name=TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))), price=?, platformid=? ,storeid=?,link=? WHERE link=?"); //We don't want random whitespaces in the name
            $queryAdd->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"], $data["link"]));
        } else {
            $queryAdd = $this->db->prepare("INSERT INTO Game(name,price,platformid,storeid,link) VALUES(TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))),?,?,?,?)"); //again, whitespace filtersn't in the database already
            $queryAdd->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"]));
        }
    }

    //Get a single entry from the Parse table
    public function getParseData(int $store, int $platform): array {
        $query = $this->db->prepare("SELECT store,platform,url, product, name, price, link, nextpage FROM Parse WHERE storeid=? AND platformid=?");
        $query->execute(array($store, $platform));
        while ($row = $query->fetch()) {
            return $row;
        }
        //Return empty array if we found nothing
        return array();
    }

    //Get a single entry from the Parse table, returns an almost empty object otherwise
    public function getParseDataObject(int $store, int $platform): ParseDataObject {
        $query = $this->db->prepare("SELECT Store.id storeid,Store.name store,Platform.name platform,Platform.id platformid,Parse.url url,product,Parse.name name,price,link,nextpage,lastupdate FROM Parse JOIN Store on Parse.storeid=Store.id JOIN Platform on Parse.platformid=Platform.id WHERE Store.id=? AND Platform.id=?");
        $query->execute(array($store, $platform));
        while ($row = $query->fetch()) {
            return new ParseDataObject($row);
        }
        //Return empty object if we found nothing
        return new ParseDataObject(array("storeid" => $store, "store" => "", "platformid" => $platform, "platform" => "", "url" => "", "product" => "", "name" => "", "price" => "", "link" => "", "nextpage" => "", "lastupdate" => null));
    }

    //Get all data from the Parse table
    public function getParseDataObjects(): array {
        $parseObjectArray = array();
        $query = $this->db->prepare("SELECT Store.id storeid,Store.name store,Platform.name platform,Platform.id platformid,Parse.url url,Parse.product product,Parse.name name,Parse.price price,Parse.link link,Parse.nextpage nextpage,lastupdate FROM Parse JOIN Store on Parse.storeid=Store.id JOIN Platform on Parse.platformid=Platform.id ORDER BY platform, store");
        $query->execute();
        while ($parse = $query->fetch()) {
            $parseObjectArray [] = new ParseDataObject($parse);
        }
        return $parseObjectArray;
    }

    public function updateParseTimestamp(int $store, int $platform) {
        $query = $this->db->prepare("UPDATE Parse SET lastupdate = NOW() WHERE storeid=? AND platformid=?");
        $query->execute(array($store, $platform));
    }

    //This function allows us to get the query how many pages we need
    public function getSearchResultAmount(string $search, int $platform): int {
        $sqlQuery = "SELECT COUNT(*) amount FROM Game WHERE name LIKE ? %platform%";
        //replace %platform% in the sql query, comes after ORDER BY
        if ($platform === 0) {
            $sqlQuery = str_replace("%platform%", "", $sqlQuery);
        } else {
            $sqlQuery = str_replace("%platform%", "AND platformid=" . $platform, $sqlQuery);
        }

        //Get amount from database
        $queryPages = $this->db->prepare($sqlQuery);
        $queryPages->execute(array("%" . $search . "%"));
        $data = $queryPages->fetch();

        //We assume it can be converted to an int, since we only use the result of count
        return (int) $data["amount"];
    }

    public function searchGames(string $search, int $platform, string $orderBy, int $orderDirection, int $page, int $pageSize): array {
        //Create empty array, will be used as return value
        $gameList = array();

        //base sql query
        $sqlQuery = "SELECT Game.name name,price,Store.name store,Platform.name platform,link,Store.url storelink,Platform.id FROM Game JOIN Platform on Game.platformid=Platform.id JOIN Store on Game.storeid=Store.id WHERE Game.name LIKE ? %platform% ORDER BY %order% LIMIT %limit%";

        //Set the string for the sort order direction
        if ($orderDirection > 0) {
            $orderDirectionString = "DESC";
        } else {
            $orderDirectionString = "ASC";
        }

        //Contains all fields which can be sorted on, just to prevent injection
        $canOrderOn = ["name", "price", "platform", "store", "link"];

        //replace %order% in the sql query, comes after ORDER BY
        if (!empty($orderBy) && in_array($orderBy, $canOrderOn)) {
            $sqlQuery = str_replace("%order%", $orderBy . " " . $orderDirectionString . ", name, price", $sqlQuery);
        } else {
            $sqlQuery = str_replace("%order%", "name, price", $sqlQuery);
        }

        //replace %platform% in the sql query, comes after ORDER BY
        if ($platform === 0) {
            $sqlQuery = str_replace("%platform%", "", $sqlQuery);
        } else {
            $sqlQuery = str_replace("%platform%", "AND Platform.id=" . $platform, $sqlQuery);
        }

        //Work with pages
        $sqlQuery = str_replace("%limit%", (($page - 1) * $pageSize) . "," . $pageSize, $sqlQuery);

        //Get data from database
        $querySearch = $this->db->prepare($sqlQuery);
        $querySearch->execute(array("%" . $search . "%"));

        //Add games
        while ($game = $querySearch->fetch()) {
            $gameList [] = new Game($game["name"], $game["price"], $game["platformid"], $game["storeid"], $game["link"]);
        }

        return $gameList;
    }

    public function getPlatformList(): array {
        $platforms = array();

        //Get all the platforms with id from the database and add to the array
        $platformData = $this->db->prepare("SELECT id, name FROM Platform");
        $platformData->execute();
        while ($platform = $platformData->fetch()) {
            $platforms [$platform["id"]] = $platform["name"];
        }

        return $platforms;
    }

    public function getStores(): array {
        $storeArray = array();

        $storeQuery = $this->db->prepare("SELECT id, name, url FROM Store");
        $storeQuery->execute();
        while ($store = $storeQuery->fetch()) {
            $storeObject = new Store($store["name"], new Url($store["url"]));
            $storeObject->setId((int) $store["id"]); //Set the id, since this is not set by the constructor of a Store object
            $storeArray [] = $storeObject;
        }

        return $storeArray;
    }

    public function addStore(Store $store) {
        $storeQuery = $this->db->prepare("INSERT INTO Store(name,url) VALUES(?,?)");
        $storeQuery->execute(array($store, $store->getUrl()));
    }

    public function removeStore(int $id) {
        $removeGamesQuery = $this->db->prepare("DELETE FROM Game WHERE storeid=?");
        $removeGamesQuery->execute(array($id));

        $removeParseQuery = $this->db->prepare("DELETE FROM Parse WHERE storeid=?");
        $removeParseQuery->execute(array($id));

        $removeStoreQuery = $this->db->prepare("DELETE FROM Store WHERE id=?");
        $removeStoreQuery->execute(array($id));
    }

    public function addParse(ParseDataObject $parse){
        //Get data from ParseDataObject object
        $data = $parse->getData();

        //Boolean for checking if an entry already exists the database
        $exists = false;

        //run query to see if the game is already in the database
        $queryFindParse = $this->db->prepare("SELECT url FROM Parse WHERE storeid=? AND platformid=?");
        $queryFindParse->execute(array($data["storeid"],$data["platformid"]));

        //If we find one, set exists to true
        while ($row = $queryFindParse->fetch()) {
            $exists = true;
            break;
        }

        //Either add or update the database entry based on if it exists already
        if ($exists) {
            $queryAdd = $this->db->prepare("UPDATE Parse SET url=?,product=?,name=?,price=?,link=?,nextpage=? WHERE storeid=? AND platformid=?");
            $queryAdd->execute(array($data["url"], $data["product"], $data["name"], $data["price"], $data["link"], $data["nextpage"],$data["storeid"], $data["platformid"]));
        } else {
            $queryAdd = $this->db->prepare("INSERT INTO Parse(storeid,platformid,url,product,name,price,link,nextpage) VALUES(?,?,?,?,?,?,?,?)");
            $queryAdd->execute(array($data["storeid"], $data["platformid"], $data["url"], $data["product"], $data["name"], $data["price"], $data["link"], $data["nextpage"]));
        }
    }
}
