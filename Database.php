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
            $this->queryFindGame = $this->db->prepare("SELECT link FROM Game WHERE link=?");
        }
    }

    public function addGame(Game $game) {
        //Get data from game object
        $data = $game->returnData();
        //Add the game to the database
        if ($this->connected) {
            $this->queryAddGame->execute(array($data["name"], $data["price"], $data["platform"], $data["store"], $data["link"]));
        }
    }

    public function printGames() {
        if ($this->connected) {
            //Open table for data
            print("<p><table>\n");
            print("<tr><th>Name</th><th>price</th><th>Platform</th><th>Store</th></tr>\n");
            
            //Set locale for euro
            setlocale(LC_MONETARY, 'nl_NL');
            
            //Get data from database
            $queyGetGames = $this->db->prepare("SELECT name,price,platform,store,link FROM Game ORDER BY name"); //sql statment
            $queyGetGames->execute();
            while ($game = $queyGetGames->fetch()) {
                print("<tr>\n");
                print("<td><a href='".$game["link"]."'>".htmlspecialchars($game["name"])."</a></td>\n");
                print("<td>". money_format('%(#1n',$game["price"])."</td>\n");
                print("<td>".htmlspecialchars($game["platform"])."</td>\n");
                print("<td>".htmlspecialchars($game["store"])."</td>\n");
                print("</tr>\n");
            }
            //Close table
            print("</table></p>\n");
        } else {
            print("<p>Couldn't fetch data from database</p>\n");
        }
    }

}
