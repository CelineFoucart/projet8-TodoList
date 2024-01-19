# ToDoList

ToDoList is an TO DO list application, developed for the Openclassrooms Program PHP/Symfony. It is developed with the Symfony Framework.

https://openclassrooms.com/projects/ameliorer-un-projet-existant-1

## Getting Started

### Prerequisites

- PHP 8.2
- composer
- MariaDB / MySQL

### Installation du projet

```bash
git clone https://github.com/CelineFoucart/projet8-TodoList.git
composer install
```

Configure the database in a .env.local file and run the migrations. 

```bash
php bin/console doctrine:database:create
php bin/console d:m:m
```

### Fixtures

Install the starting data when the project is in dev environment:

```bash
php bin/console doctrine:fixtures:load
```

### Testing the application

```bash
php bin/phpunit
```

Run phpstan 
```bash
vendor/bin/phpstan analyse src tests
```