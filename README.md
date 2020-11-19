##About the project
This project was build in symfony 5.1 using the Messenger module to create in an asynchronous manner the invoices 
imported from a csv file, the invoice handler validate the data receives from the file and calculates the selling price. 
The project relays in the orm transport channel but it could be replaced in an easy manner for another ones like AMQP or Amazon SQS.

###Pre-requisites
- Docker

### Set-up the project
The application is provisioned with docker.

Create an `.env` file in the  `docker\` directory and add the proper values to the following parameters:
```
APP_ENV=dev
APP_SECRET= 
DATABASE_NAME=
DATABASE_USER=
DATABASE_PASSWORD=
DATABASE_ROOT_PASSWORD=
```

The commands shown below will provide the application with external libraries and it will create a database to store the info related to the invoice reports  
```bash
$ cd docker/
$ docker-compose up -d
```
### Workers
To consume the invoice messages a worker must to stay running in order to consume them from the queue, execute the command below to start a worker
```bash
$ docker-compose run php-fpm bin/console messenger:consume async
```

### Run Test
To run the test suite execute the command below: 
```bash
$ docker-compose run php-fpm php bin/phpunit --colors=always --testdox
```
