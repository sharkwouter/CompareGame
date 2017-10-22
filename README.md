How to use:
 - Import base-database.sql
 - Create database user with access to the table Game
 - Fill out config.example
 - Rename config.example to config.php
 - Install apache and php
 - Copy all php and html files into the webroot of apache

Files
 - Parser.php: extracts Game objects from a web page
 - Game.php: simple game objects containing name, price, store, url
 - Database.php: this one does the interraction with the database
 - Import.php: bridge between a parser and the database
 - base-database.sql: the base of the actual database, without this the application won't work