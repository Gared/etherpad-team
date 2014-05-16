# EPLite-Team
This software adds team functionality to [Etherpad Lite](https://github.com/ether/etherpad-lite/).
It uses the framework cakephp and mysql as database. For performance purposes the API-calls will be cached with redis.

## Requirements
* Etherpad
* MySQL
* Redis
* Mailserver

## Installation
* Checkout this repo: git checkout https://github.com/Gared/etherpad-team.git
* Create a database and create the tables from the script in app/Config/Schema/schema.sql
* Copy the config files in app/Config/(database.php.default, email.php.default, eplite.php.default) without the ending ".default" and change the settings

## TODOs
* Make caching optional (other database)
* Better integration of categories
* Differnt team configurations (list all open pads for non-team members, default pad text)

## This software uses the following libraries
* Cakephp (MIT License) http://cakephp.org
* EtherpadLiteClient (Apache License) https://github.com/TomNomNom/etherpad-lite-client