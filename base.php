<?php
//Add 
require_once 'classes/Config.php';
require_once 'classes/Database2.php';

$config = new Config();
$configDatabase = $config->getConfig("database", array("database","ip","port","username","password"));

$db = new Database($configDatabase);