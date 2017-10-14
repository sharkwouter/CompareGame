<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Product
 *
 * @author wouter
 */
class Game {
    //put your code here
    private $name;
    private $price;
    private $platform;
    private $store;
    private $link;
    
    public function __construct(string $name, string $price, string $platform, string $store, string $link) {
        $this->name = $name;
        $this->price = $price;
        $this->platform = $platform;
        $this->store = $store;
        $this->link = $link;
    }
    
    public function returnSQL(string $table){
        //Base sql statement
        $sql = "INSERT INTO %table%(name,price,platform,store,link) values(\"%name%\", %price%, \"%platform%\", \"%store%\",\"%link%\")";
        
        //Add data
        $sql = str_replace("%table%", $table, $sql);
        $sql = str_replace("%name%", htmlspecialchars($this->name), $sql);
        $sql = str_replace("%price%", (float) $this->price, $sql);
        $sql = str_replace("%platform%", htmlspecialchars($this->platform), $sql);
        $sql = str_replace("%store%", htmlspecialchars($this->store), $sql);
        $sql = str_replace("%link%", htmlspecialchars($this->link), $sql);
        
        return $sql;
    }
    
    public function returnData(){
        $data = array(
            "name" => $this->name,
            "price" => $this->price,
            "platform" => $this->platform,
            "store" => $this->store,
            "link" => $this->link
        );
        return $data;
    }
    
    public function printData(){
        echo "<p><h3>".$this->name."</h3></p>".
             "Price: ".$this->price."<br>".
             "platform: ".$this->platform."<br>".
             "store: ".$this->store."<br>".
             "link: ".$this->link."<br>";
    }
}
