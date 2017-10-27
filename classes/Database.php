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
    private $queryParseData;
    
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
            $this->queryAddGame = $this->db->prepare("INSERT INTO Game(name,price,platform,store,link) VALUES(TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))),?,?,?,?)"); //We don't want random whitespaces in the name
            $this->queryUpdateGame = $this->db->prepare("UPDATE Game SET name=TRIM((REPLACE(REPLACE(?,'\t',''),'\n',''))), price=?, platform=? ,store=?,link=? WHERE link=?"); //again, whitespace filter
            $this->queryFindGame = $this->db->prepare("SELECT link FROM Game WHERE link=?");
            $this->queryParseData = $this->db->prepare("SELECT company,platform,url, product, name, price, link, nextpage FROM Parse WHERE company=? AND platform=?");
        }
        
        //get user input
        $this->platformstring = filter_input(INPUT_GET, "platform");
        $this->orderby = filter_input(INPUT_GET, "orderby");
        $this->orderDesc = filter_input(INPUT_GET, "desc");
    }

    //Add a game to the database
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
    public function printGames() {
        $this->searchGames("");
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
                print("<td><a href='" . $game["link"] . "'>" . htmlspecialchars($game["gamename"]) . "</a></td>\n");
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
        if(empty($this->platformstring)){
            return " <a href='index.php?orderby=".$field."&search=".$search."'>↑</a> ". ucfirst($field)." <a href='index.php?orderby=".$field."&desc=true&search=".$search."'>↓</a>";
         } else {
            return " <a href='index.php?orderby=".$field."&search=".$search."&platform=".$this->platformstring."'>↑</a> ". ucfirst($field)." <a href='index.php?orderby=".$field."&desc=true&search=".$search."&platform=".$this->platformstring."'>↓</a>";
        }
    }
    
    //Makes the sql query used when searching and executes it
    private function executeSearch($search){
        //base sql query
        $sqlQuery = "SELECT Game.name gamename,price,Company.name store,Platform.name platform,link,Company.url storelink,Platform.id FROM Game JOIN Platform on Game.platform=Platform.id JOIN Company on Game.store=Company.id WHERE Game.name LIKE ?";
        
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
                    $sqlQuery = $sqlQuery." ORDER BY gamename";
                    break;
                case "price":
                    $sqlQuery = $sqlQuery." ORDER BY price";
                    break;
                case "platform":
                    $sqlQuery = $sqlQuery." ORDER BY platform";
                    break;
                case "store":
                    $sqlQuery = $sqlQuery." ORDER BY store";
                    break;
            }
        } else {
            $sqlQuery = $sqlQuery." ORDER BY gamename, price";
        }
        
        //Set the order to descending or not, sort by name and price after whatever we are sorting on
        if(isset($this->orderDesc)){
            $sqlQuery = $sqlQuery." DESC,gamename,price";
        } else {
            $sqlQuery = $sqlQuery.",gamename,price";
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
    
    //Print the table in update.php
    public function printUpdate(){
        //Open table with header
        print("<table>\n");
        $fields = array("store","platform","url","run");
            print("<tr>");
           foreach ($fields as $field) {
                print("<th>".$field."</th>");
            }
            print("</tr>\n");
        //Execute sql
        $query = $this->db->prepare("SELECT Company.id storeid,Company.name store,Platform.name platform,Platform.id platformid,Parse.url url,Parse.product,Parse.name,Parse.price,Parse.link,Parse.nextpage FROM Parse JOIN Company on Parse.company=Company.id JOIN Platform on Parse.platform=Platform.id ORDER BY platform, store");
        $query->execute();
        while($parse = $query->fetch()){
            print("<tr><td>".$parse["store"]."</td><td>".$parse["platform"]."</td><td><a href='".$parse["url"]."'>link</a></td>\n");
            //Create run button
            print("<td><form method='post'>");
            
            print("<input type='hidden' name='storeid' value='".$parse["storeid"]."' />");
            print("<input type='hidden' name='platformid' value='".$parse["platformid"]."' />");
            print("<input type='submit' value='Run' />");
            print("</form></td></tr>\n");
        }
    }
    
    //Get data from the Parse database
    public function getParseData(int $company, int $platform){
        $this->queryParseData->execute(array($company,$platform));
        while($row = $this->queryParseData->fetch()){
            return $row;
        }
    }
    
    //Prints the dropdown for the search form in index.php
    public function printPlatformDropdown(){
        //Create empty array
        $platforms = array();
        
        //Get all the platforms with id from the database and add to the array
        $platformData = $this->db->prepare("SELECT id, name FROM Platform");
        $platformData->execute();
        while($platform = $platformData->fetch()){
            $platforms [$platform["id"]] = $platform["name"];
        }
        
        //Print the dropdown menu itself
        print("<select name='platform' onchange='this.form.submit()'>\n");
        print("<option value=0>--</option>\n");
        foreach($platforms as $id => $name){
            //Highlight the currently set platform
            if($this->platformstring == $id) {
                print("<option selected value='".$id."'>".$name."</option>\n");
            } else {
                print("<option value='".$id."'>".$name."</option>\n");
            }
        }
        print("</select>\n");
    }
}
