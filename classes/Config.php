<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Config
 *
 * @author wouter
 */
class Config {

    private $configFile = "config.ini";
    private $configData;
    private $failed = false;

    public function __construct() {
        //Load config, we assume that if a file is found the syntax is correct, otherwise you'll get a syntax error anyway
        if (is_file($this->configFile)) {
            $this->configData = parse_ini_file($this->configFile, true);
        } else {
            print("<p><h3>The config file " . $this->configFile . " could not be loaded</h3></p>");
            $this->failed = true;
        }
    }

    //Get the data from a specific part of the config file
    public function getConfig(string $part, array $expectedData) {
        //If we have the config file loaded and it contained the part we asked for
        if (!$this->failed && array_key_exists($part, $this->configData)) {
            //This variable is false if we are missing data
            $canReturnData = true;
            //Make sure we have all the data we need
            foreach ($expectedData as $field) {
                if (!array_key_exists($field, $this->configData[$part])) {
                    $canReturnData = false;
                    print("The entry " . $field . " is missing in " . $this->configFile . " under ".$part."!");
                }
            }
            //Return the data if we are sure it is a complete set
            if ($canReturnData) {
                return $this->configData["database"];
            }
        }
        //If all else fails, return an empty array
        return array();
    }

}
