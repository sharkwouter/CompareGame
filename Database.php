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
        } catch(PDOException $ex) {
            print("<p><h3>The database connection has failed</h3></p>");
        }
        
        //Prepare queries we may need
        if($this->connected){
            $this->queryAddGame = $this->db->prepare("INSTERT into Game(name,price,platform,store,link) VALUES(?,?,?,?,?)");
            $this->queryFindGame = $this->db->prepare("SELECT link FROM Game WHERE link=?");
        }
    }

    public function addGame(Game $game){
        //Get data from game object
        $data = $game->returnData();
        
        
    }
}
