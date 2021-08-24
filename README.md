ToDoList
========
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/15760ae5ae2e405190f2ac14de670289)](https://www.codacy.com/gh/Jersey276/TristanLefevre_8_28072021/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Jersey276/TristanLefevre_8_28072021&amp;utm_campaign=Badge_Grade)

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Installation
1.  clone this gitHub project (on a git CLI)
``` 
git clone https://github.com/Jersey276/TristanLefevre_8_28072021.git
``` 
2.  install all Composer dependencies (on console at base project folder)
```
composer update 
```
3.  Create database and lunch migration
```
php bin/console database:create
php bin/console doctrine:migration:migrate
```
4.  optional lunch fixtures for test application with basic data
```
php bin/console doctrine:fixtures:load --env=dev
```
