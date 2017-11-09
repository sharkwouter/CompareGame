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
CREATE TABLE IF NOT EXISTS `comparegames`.`Company` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
    `url` VARCHAR(100) NOT NULL,
PRIMARY KEY (`id`));

CREATE TABLE IF NOT EXISTS `comparegames`.`Platform` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(45) NOT NULL,
PRIMARY KEY (`id`));

CREATE TABLE IF NOT EXISTS `comparegames`.`Parse` (
    `company` INT NOT NULL,
    `platform` INT NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `product` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `price` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) NOT NULL,
    `nextpage` VARCHAR(255) NOT NULL DEFAULT '',
    `lastupdate` DATETIME NULL,
    PRIMARY KEY (`company`,`platform`),
    FOREIGN KEY (company) REFERENCES Company(id),
    FOREIGN KEY (platform) REFERENCES Platform(id));

CREATE TABLE IF NOT EXISTS `comparegames`.`Game` (
  `name` VARCHAR(100) NOT NULL,
  `price` FLOAT NOT NULL,
  `platform` INT NOT NULL,
  `store` INT NULL,
  `link` VARCHAR(250) NOT NULL,
PRIMARY KEY (`link`),
FOREIGN KEY (store) REFERENCES Company(id),
FOREIGN KEY (platform) REFERENCES Platform(id));


-- Add some standard data to database
INSERT INTO `Company` (`name`,`url`) VALUES
('Nedgame','https://www.nedgame.nl/'),
('Mariocube','http://mariocube.nl/'),
('Game Outlet','http://game-outlet.nl/');

INSERT INTO `Platform` (`name`) VALUES
('Xbox One'),
('Gamecube');

INSERT INTO `Parse` (`company`,`platform`,`url`,`product`,`name`,`price`,`link`,`nextpage`) VALUES
(1, 1, 'https://www.nedgame.nl/xbox-one/games/', '//table[@class="productTable"]/tbody/tr', '//td[@class="title"]/div[@class="titlewrapper"]/a/h3', '//td[@class="buy"]/div[@class="koopdiv"]/div[@class="currentprice"]', '//td[@class="title"]/div[@class="titlewrapper"]/a', '//div[@id="datatablePagination"]/div/span/a'),
(2, 2, 'https://www.mariocube.nl/GameCube_Winkel.php?t=Games&p=1', '//div[@id="main_midden"]/div[@id="winkelblokl"]|//div[@id="main_midden"]/div[@id="winkelblokr"]', '//div[@id="wtitel"]/a|//div[@id="wtitels"]/a', '//div[@id="wprijs"]', '//div[@id="wtitel"]/a|//div[@id="wtitels"]/a', '//center//div[@id="kopje"]/a'),
(3, 1, 'http://www.game-outlet.nl/xbox/xbox-one/games-nieuw/?limit=24', '//div[@class="item"]', '//h3[@class="item-name"]', '//span[@class="item-price"]', '//h3[@class="item-name"]/a', '//ul[@class="pagination"]/li/a');