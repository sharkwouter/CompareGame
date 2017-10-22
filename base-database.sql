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

CREATE TABLE IF NOT EXISTS `comparegames`.`Game` (
  `name` VARCHAR(100) NOT NULL,
  `price` FLOAT NOT NULL,
  `platform` VARCHAR(64) NOT NULL,
  `store` VARCHAR(64) NOT NULL,
  `link` VARCHAR(250) NOT NULL,
PRIMARY KEY (`link`));