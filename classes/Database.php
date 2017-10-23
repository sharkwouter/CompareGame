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
    public $connected = false;
    //Query attributes
    private $queryAddGame;
    private $queryFindGame;
    private $queryUpdateGame;
    
    //User input
    private $platformstring;
    private $orderby;
    private $orderDesc;

    public function __construct(string $dbname, string $dbip, int $dbport, string $dbuser, string $dbpass) {
        try {
            $this->db = new PDO("mysql:host=" . $dbip . ";dbname=" . $dbname . ";port=" . $dbport, $dbuser, $dbpass);
            $this->connected = TRUE;
        } catch (PDOException $ex) {
            print("<p><h3>The database connection has failed</h3></p>");
        }

        //Prepare queries we may need
        if ($this->connected) {
            $this->queryAddGame = $this->db->prepare("INSERT INTO Game(name,price,platform,store,link) VALUES(?,?,?,?,?)");
            $this->queryUpdateGame = $this->db->prepare("UPDATE Game SET name=?, price=?, platform=? ,store=?,link=? WHERE link=?");
            $this->queryFindGame = $this->db->prepare("SELECT link FROM Game WHERE link=?");
        }
        
        //get user input
        $this->platformstring = filter_input(INPUT_GET, "platform");
        $this->orderby = filter_input(INPUT_GET, "orderby");
        $this->orderDesc = filter_input(INPUT_GET, "desc");
    }

    public function addGame(Game $game) {
        //Get data from game object
        $data = $game->returnData();
        //Add the game to the database
        if ($this->connected) {
            //Check if the game isn't in the database already
            $exists = false;
            $this->queryFindGame->execute(array($data["link"]));
            while ($game = $this->queryFindGame->fetch()) {
                $exists = true;
                break;
            }
            //Either add or update the database entry based on if it exists already
            if ($exists) {
                $this->queryUpdateGame->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"], $data["link"]));
            } else {
                $this->queryAddGame->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"]));
            }
        }
    }

    public function searchGames(string $search) {
        //Set locale for euro
        setlocale(LC_MONETARY, 'nl_NL');
        
        if ($this->connected) {
            //Open table for data
            print("<p><table>\n");
            
            //Print table header with order buttons
            $fields = array("name","price","platform","store");
            print("<tr>");
           foreach ($fields as $field) {
                print("<th>".$this->getOrderLinks($field, $search)."</th>");
            }
            print("</tr>\n");
            //Get the sql
            $result = $this->executeSearch($search);
            while ($game = $result->fetch()) {
                print("<tr>\n");
                print("<td><a href='" . $game["link"] . "'>" . htmlspecialchars($game["name"]) . "</a></td>\n");
                print("<td>" . money_format('%(#1n', $game["price"]) . "</td>\n");
                print("<td>" . htmlspecialchars($game["platform"]) . "</td>\n");
                print("<td><a href='" . $game["storelink"] . "'>" . htmlspecialchars($game["store"]) . "</a></td>\n");
                print("</tr>\n");
            }
            //Close table
            print("</table></p>\n");
        } else {
            print("<p>Couldn't fetch data from database</p>\n");
        }
    }

    //Generates links for the order in which
    private function getOrderLinks(string $field, string $search){
        return " <a href='index.php?orderby=".$field."&search=".$search."'>↑</a> ". ucfirst($field)." <a href='index.php?orderby=".$field."&desc=true&search=".$search."'>↓</a>";
    }
    
    //Makes the sql query used when searching and executes it
    private function executeSearch($search){
        //base sql query
        $sqlQuery = "SELECT Game.name name,price,Company.name store,Platform.name platform,link,Company.url storelink,Platform.id FROM Game JOIN Platform on Game.platform=Platform.id JOIN Company on Game.store=Company.id WHERE Game.name LIKE ?";
        
        //Add platform
        if(isset($this->platformstring) && $this->platformstring > 0){
            $sqlQuery = $sqlQuery." AND Platform.id=?";
        } else {
            $this->platformstring = "";
        }
            
        //set orderby
        if(isset($this->orderby)) {
            switch ($this->orderby) {
                case "name":
                    $sqlQuery = $sqlQuery."ORDER BY name";
                    break;
                case "price":
                    $sqlQuery = $sqlQuery."ORDER BY price";
                    break;
                case "platform":
                    $sqlQuery = $sqlQuery."ORDER BY platform";
                    break;
                case "store":
                    $sqlQuery = $sqlQuery."ORDER BY store";
                    break;
            }
        } else {
            $sqlQuery = $sqlQuery."ORDER BY name,price";
        }
        
        //Set the order to descending or not, sort by name and price after whatever we are sorting on
        if(isset($this->orderDesc)){
            $sqlQuery = $sqlQuery." DESC,name,price";
        } else {
            $sqlQuery = $sqlQuery.",name,price";
        }

        //Get data from database
        $queryGetGames = $this->db->prepare($sqlQuery); //sql statment
        
        //Execute sql
        if(empty($this->platformstring)){
            $queryGetGames->execute(array("%".$search."%"));
        } else {
            $queryGetGames->execute(array("%".$search."%",$this->platformstring));
        }
        //Return the result
        return $queryGetGames;
    }
    
    public function printGames() {
        $this->searchGames("");
    }

}
