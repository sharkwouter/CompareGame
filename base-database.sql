/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  wouter
 * Created: Oct 15, 2017
 */

-- Create the database
DROP SCHEMA IF EXISTS `comparegames`;
CREATE SCHEMA IF NOT EXISTS `comparegames`;
USE `comparegames`;

-- Create tables
CREATE TABLE IF NOT EXISTS `comparegames`.`Store` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    `url` VARCHAR(100) NOT NULL,
PRIMARY KEY (`id`));

CREATE TABLE IF NOT EXISTS `comparegames`.`Platform` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
PRIMARY KEY (`id`));

CREATE TABLE IF NOT EXISTS `comparegames`.`Parse` (
    `storeid` INT NOT NULL,
    `platformid` INT NOT NULL,
    `url` VARCHAR(255) NOT NULL DEFAULT '',
    `product` VARCHAR(255) NOT NULL DEFAULT '',
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `price` VARCHAR(255) NOT NULL DEFAULT '',
    `link` VARCHAR(255) NOT NULL DEFAULT '',
    `nextpage` VARCHAR(255) NOT NULL DEFAULT '',
    `lastupdate` DATETIME NULL,
    PRIMARY KEY (`storeid`,`platformid`),
    FOREIGN KEY (storeid) REFERENCES Store(id),
    FOREIGN KEY (platformid) REFERENCES Platform(id));

CREATE TABLE IF NOT EXISTS `comparegames`.`Game` (
  `name` VARCHAR(100) NOT NULL,
  `price` FLOAT NOT NULL,
  `platformid` INT NOT NULL,
  `storeid` INT NOT NULL,
  `link` VARCHAR(250) NOT NULL,
PRIMARY KEY (`link`),
FOREIGN KEY (storeid) REFERENCES Store(id),
FOREIGN KEY (platformid) REFERENCES Platform(id));


-- Add some standard data to database
INSERT INTO `Store` (`name`,`url`) VALUES
('Nedgame','https://www.nedgame.nl/'),
('Mariocube','http://mariocube.nl/'),
('Game Outlet','http://game-outlet.nl/'),
('Intertoys','https://www.intertoys.nl/');

INSERT INTO `Platform` (`name`) VALUES
('Xbox One'),
('Gamecube');

INSERT INTO `Parse` (`storeid`,`platformid`,`url`,`product`,`name`,`price`,`link`,`nextpage`) VALUES
(1, 1, 'https://www.nedgame.nl/xbox-one/games/', '//table[@class="productTable"]/tbody/tr', '//td[@class="title"]/div[@class="titlewrapper"]/a/h3', '//td[@class="buy"]/div[@class="koopdiv"]/div[@class="currentprice"]', '//td[@class="title"]/div[@class="titlewrapper"]/a', '//div[@id="datatablePagination"]/div/span/a'),
(2, 2, 'https://www.mariocube.nl/GameCube_Winkel.php?t=Games&p=1', '//div[@id="main_midden"]/div[@id="winkelblokl"]|//div[@id="main_midden"]/div[@id="winkelblokr"]', '//div[@id="wtitel"]/a|//div[@id="wtitels"]/a', '//div[@id="wprijs"]', '//div[@id="wtitel"]/a|//div[@id="wtitels"]/a', '//center//div[@id="kopje"]/a'),
(3, 1, 'http://www.game-outlet.nl/xbox/xbox-one/games-nieuw/?limit=24', '//div[@class="item"]', '//h3[@class="item-name"]', '//span[@class="item-price"]', '//h3[@class="item-name"]/a', '//ul[@class="pagination"]/li/a'),
(4,1,'https://www.intertoys.nl/c/games-en-spelcomputers/xbox-one/xbox-one-games/809557','//a[@class=\"product card\"]','//div[@class=\"content\"]/h4','////div[@class=\"ui price\"]','//a[@class=\"product card\"]','//a[@aria-label=\"Next\"]');