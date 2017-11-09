<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Store
 *
 * @author wouter
 */

require_once 'classes/Url.php';

class Store {
    private $name;
    private $url;
    private $id;
    
    public function __construct(string $name, Url $url){
        $this->name = $name;
        $this->url = $url;
    }
    
    public function setId(int $id){
        $this->id = $id;
    }
    
    //Returns -1 if id is not set
    public function getId() : int{
        if(isset($this->id)){
            return $this->id;
        }
        return -1;
    }
    
    public function getUrl() : url {
        return $this->url;
    }
    
    public function __toString() : string {
        return $this->name;
    }
}
