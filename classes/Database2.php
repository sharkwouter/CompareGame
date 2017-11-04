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
    private function printError(string $error): void {
        print("<p>An issue has been found: " . $error . "</p>");
        exit();
    }

    //Add a game to the database
    public function addGame(Game $game): void {
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
            $queryAdd = $this->db->prepare("UPDATE Game SET name=TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))), price=?, platform=? ,store=?,link=? WHERE link=?"); //We don't want random whitespaces in the name
            $queryAdd->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"], $data["link"]));
        } else {
            $queryAdd = $this->db->prepare("INSERT INTO Game(name,price,platform,store,link) VALUES(TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))),?,?,?,?)"); //again, whitespace filtersn't in the database already
            $queryAdd->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"]));
        }
    }

    //Get data from the Parse table
    public function getParseData(int $company, int $platform): array {
        $query = $this->db->prepare("SELECT company,platform,url, product, name, price, link, nextpage FROM Parse WHERE company=? AND platform=?");
        $query->execute(array($company, $platform));
        while ($row = $query->fetch()) {
            return $row;
        }
    }

    public function searchGames(string $search, int $platform, string $orderBy, int $orderDirection, int $page, int $pageSize): array {
        //Create empty array, will be used as return value
        $gameList = array();
        
        //base sql query
        $sqlQuery = "SELECT Game.name name,price,Company.name store,Platform.name platform,link,Company.url storelink,Platform.id FROM Game JOIN Platform on Game.platform=Platform.id JOIN Company on Game.store=Company.id WHERE Game.name LIKE ? %platform% ORDER BY %order% LIMIT %limit%";

        //Set the string for the sort order direction
        if($orderDirection > 0){
            $orderDirectionString = "DESC";
        } else {
            $orderDirectionString = "ASC";
        }
        
        //Contains all fields which can be sorted on, just to prevent injection
        $canOrderOn = ["name","price","platform","store","link"];
        
        //replace %order% in the sql query, comes after ORDER BY
        if(!empty($orderBy) && in_array($orderBy, $canOrderOn)){
            $sqlQuery = str_replace("%order%", $orderBy." ".$orderDirectionString.", name, price", $sqlQuery);
        } else {
            $sqlQuery = str_replace("%order%", "name, price", $sqlQuery);
        }
        
        //replace %platform% in the sql query, comes after ORDER BY
        if ($platform === 0) {
            $sqlQuery = str_replace("%platform%", "", $sqlQuery);
        } else {
            $sqlQuery = str_replace("%platform%", "AND Platform.id=".$platform, $sqlQuery);
        }
        
        //Work with pages
        $sqlQuery = str_replace("%limit%", ($page*$pageSize).",".$pageSize, $sqlQuery);
        
        //Get data from database
        $querySearch = $this->db->prepare($sqlQuery);
        $querySearch->execute(array("%" . $search . "%"));
        
        //Add games
        while($game = $querySearch->fetch()){
            $gameList [] = new Game($game["name"], $game["price"], $game["platform"], $game["store"], $game["link"]);
        }
        
        return $gameList;
    }
    
    public function getPlatformList() : array {
        $platforms = array();

        //Get all the platforms with id from the database and add to the array
        $platformData = $this->db->prepare("SELECT id, name FROM Platform");
        $platformData->execute();
        while ($platform = $platformData->fetch()) {
            $platforms [$platform["id"]] = $platform["name"];
        }
        
        return $platforms;
    }

}
