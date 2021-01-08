Real estate management web application

Stack : Symfony, MySQL

Instructions for database : 
  1. Install MySQL
  2. Create an user with password  and the database 
  3. In the .env file change db_user,db_password and db_name in DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
  4. In src folder run : 
  
    composer install 
    php bin/console doctrine:migrations:migrate 


Home view : 
![alt text](https://i.ibb.co/1JCtcRL/home.png)
  



  
    

