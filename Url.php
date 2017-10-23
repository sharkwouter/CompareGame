<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Url
 *
 * @author wouter
 */
class Url {
    //variables
    private $urlString;
    public function __construct(string $url) {
        $this->urlString = $url;
    }
    public function getBase(){
        $parser = parse_url($this->urlString);
        if(isset($parser["scheme"]) && $parser["host"]) {
            return $parser["scheme"]."://".$parser["host"];
        } else {
            return "";
        }
    }
    
    public function __toString() {
        return $this->urlString;
    }
}
