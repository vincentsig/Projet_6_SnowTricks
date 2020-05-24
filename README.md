# Projet_6_SnowTricks

SnowTrick is a collaborative website to introduce snowboarding to the general public and help them to have a better knowloedge of the different tricks.

## Introduction
This is my first Symfony projects of my apprenticeship training with OpenClassrooms .
The main constraint, was to not use any external Bundles during the develpment except for the initial data (symfony bundles).

## Code quality


## Information


## Development environment 

- PHP  7.2.10
- Symfony 4.4.8
- Apache 2.4.35
- MySQL 5.7.23
- Axios 0.19.2
- Bootstrap 4.4.1
- Core-js 3.6.4
- Font-awesome 4.7.0
- Jquery 3.4.1
- Lightbox2 2.11.1
- Popper.js 1.16.1
- Symfony/webpack-encore 0.27.0
- Webpack-notifier 1.8.0


## Installation

**1. Download and clone the github repositoy:**  

      https://github.com/vincentsig/Projet_6_SnowTricks.git

**2. Install the back-end  dependencies**

      composer install

**3. Install npm for the front-end dependencies:**
    
      npm install    
      
**4. Build assets with webpack Encore**
    
      ./node_modules/.bin/encore dev --watch

**5. Configure the .env**

      DATABASE_URL=mysql: //db_user:db_password@127.0.0.1:3306/db_name

**6. Create the Database**

      php bin/console doctrine:database:create

**7. Update schema**
 
      php bin/console doctrine:schema:update --force

**8. Load the dataFixtures**

      php bin/console doctrine:fixtures:load
      
**9. Run PHP's built-in Web Server**

      bin/console server:run
      
## Enjoy !

  Now you can use the application. If you have load the fixtures you can use the users account to add, edit, or delete some Tricks.You can also post comments.  
     login: username-1 password: password
            username-2 password: password 
            etc...
      

      
    
    
