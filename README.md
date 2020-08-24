# Instafrep

Internal social network made by Infrep students for Infrep students.

## Contributing

Before getting started, read [CONTRIBUTING.md](CONTRIBUTING.md) carefully !

## Main commands

- Start the dev server 
    ````
    symfony server:start
    ````
  
- Creating a new migeration file (ie. after having updated an **Entity**, ...) 
  ````
  symfony console make:migration
  ````
  
- Recreate a brand new database 
    ````
    symfony console doctrine:database:drop --force
    symfony console doctrine:database:create
    symfony console doctrine:migrations:migrate
    symfony console doctrine:fixtures:load
    ````
  
- Check all the available routes 
    ````
    symfony console debug:router
    ````