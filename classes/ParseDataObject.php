<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParseDataObject
 *
 * @author wouter
 */
class ParseDataObject {
     private $data = array();
     private $expectedData = array("storeid","store","platformid","platform","url");
    
    public function __construct($data) {
        //Temporary variables
        $tempArray= array();
        $count = 0;
        
        //Check if how much of the data we want can be found in the data we got
        foreach($this->expectedData as $datapiece){
            if(array_key_exists($datapiece, $data)) {
                $tempArray[$datapiece] = $data[$datapiece];
                $count++;
            }
        }
        //Only save the data if it is complete
        if($count == sizeof($this->expectedData)) {
            $this->data = $tempArray;
        }
    }
    
    public function getData() : array {
        return $this->data;
    }
    
    //Return something if this object contains data
    public function __toString() : string {
        if(!empty($this->data)){
            return $this->data["store"].", ".$this->data["platform"];
        }
        return "";
    }
    
    public function getUrl() : string {
        return "".$this->data["url"];
    }
}
