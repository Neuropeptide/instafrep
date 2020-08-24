# Contributing

## Requirements

- PHP 7.4.1 (or higher)
- MySQL 5.7 (or higher), or MariaDB 10.4 (or higher)
- Composer
- *optionnal* NodeJS 

## Installation

1. Clone the repo locally
    ```sh
   git clone https://gitlab.com/infrep33-web/fullstack-2020/tp-instafrep-teamwork.git 
   ```
   
2. Install the dependencies
    ```sh
   composer install
   npm install
    ```

3. Set up your environnement file `.env.local` :
    - copy the `.env` example file and name it `.env.local`
    - make sure you define at least the `DATABASE_URL`
    - you can add any other environment variable if you need to 

4. Create the database
    ````sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```` 
   
5. _(Optionnal)_ Load the fixtures
    ````sh
    symfony console doctrine:fixtures:load
    ````
   
## Develop

1. Create a new branch from the `develop` branch

2. Once you are on your own branch, make as many commit as possible ! You can even create other branches from it, if you need to.

3. Push your branch on a regular basis (once an hour seems to be a good pace)

4. When your feature is ready, or a major milestone is reached, open a **merge request** on GitLab, from your branch into the `develop`    