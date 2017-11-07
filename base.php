<?php
//Add 
require_once 'classes/Config.php';
require_once 'classes/Database.php';

$config = new Config();
$configDatabase = $config->getConfig("database", array("database","ip","port","username","password"));

$GLOBALS['db'] = new Database($configDatabase);

//Get GET data as an int
function getGetAsInt(string $name, int $default) : int {
    //This function returns false if it isn't an int
    $int = (int)filter_input(INPUT_GET, $name, FILTER_VALIDATE_INT);
    
    //Check if int isn't false
    if($int == false || $int < 0){
        return $default;
    }
    return $int;
}

//Get GET data as a string
function getGetAsString(string $name, string $default) : string {
    //Get the get data
    $string = filter_input(INPUT_GET, $name);
    
    //Check if the string isn't empty
    if(empty($string)){
        return $default;
    }
    return $string;
}