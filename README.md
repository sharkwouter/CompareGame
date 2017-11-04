How to use:
 - Import base-database.sql
 - Create database user with access to the table Game
 - Fill out config-example.ini
 - Rename config-example.ini to config.ini
 - Install apache and php
 - Copy all php and html files into the webroot of apache

Classes:
 - Config.php: loads data from the configuration file
 - Database.php: this one does the interaction with the database
 - Game.php: an object containing a single game, contains name, price, store, url
 - Import.php: initializes updating games for a specific platform from a specific website
 - Navbar.php: draws the menubar on top of every page
 - ParseDataObject.php: an object containing data from a single entry from the Parse table
 - Parser.php: extracts Game objects from a website
 - Store.php: an object containing a single store, only contains the name and the url. Isn't being used currently
 - Url.php: contains a url and can extract data from it, currently very basic

 - Import.php: bridge between a parser and the database

Other files:
 - base-database.sql: the base of the actual database, without this the application won't work
 - base.php: to be included on every page, creates a database object and has some basic functions
 - index.php: main page, allows you to search for games in the database
 - update.php: allows you to update the games in the database based on entries in the Parse table 