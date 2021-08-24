ToDoList
========

Base du projet #8 : Améliorez un projet existant

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Installation
1. clone this gitHub project (on a git CLI)
``` 
git clone https://github.com/Jersey276/TristanLefevre_8_28072021.git
``` 
2. install all Composer dependencies (on console at base project folder)
```
composer update 
```
3. Create database and lunch migration
```
php bin/console database:create
php bin/console doctrine:migration:migrate
```
4. optional lunch fixtures for test application with basic data
```
php bin/console doctrine:fixtures:load --env=dev
```
