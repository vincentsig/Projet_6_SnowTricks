# Projet_6_SnowTricks

## Introduction
This is my first Symfony projects of my apprenticeship training with OpenClassrooms .
The main constraint, was to not use any external Bundles during the develpment except for the initial data (symfony bundles).

## Information

SnowTrick is a collaborative website to introduce snowboarding to the general public and help them to have a better knowloedge of the different tricks.

You just have to create your account and when you are log-in you have the possibility to add some new snow tricks. That's the point! We want you to collaborate with the community to promote the visibility of this amazing sport to the public. 
You can also participate to improve the content of the tricks by adding some new pictures or youtube and daylimotion videos. And you know what, you can even add your own media if you have some nice footage!!

## Code quality

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e654ff11b147484aaeb3edc9e1534021)](https://www.codacy.com/manual/vincentsig/Projet_6_SnowTricks?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=vincentsig/Projet_6_SnowTricks&amp;utm_campaign=Badge_Grade)



## Development environment 

- PHP  7.2.10
- Symfony 4.4.8
- Apache 2.4.35
- MySQL 5.7.23

## Installation

**1. Download or clone the github repository:**  

      https://github.com/vincentsig/Projet_6_SnowTricks.git

**2. Install the back-end  dependencies**

      composer install

**3. Install npm for the front-end dependencies:**
    
      npm install    
      
**4. Build assets with webpack Encore**
    
      ./node_modules/.bin/encore dev --watch

**5. Configure the .env**

      DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

**6. Create the Database**

      php bin/console doctrine:database:create

**7. Update schema**
 
      php bin/console doctrine:schema:update --force

**8. Load the dataFixtures**

      php bin/console doctrine:fixtures:load
      
**9. Run PHP's built-in Web Server**

      bin/console server:run
      
## Enjoy !

  Now you can use the application. If you have load the fixtures you can use the users account already created.  
     login:    
     username-1 password: password    
     username-2 password: password    
     etc...
      

      
    
    
