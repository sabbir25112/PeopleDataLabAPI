This application for getting the personal emails from company email using `peopledatalabs.com` **API**

## Installation

Create `.env` by copying `.env.example`

Update database information in `.env`

````
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
````

Install dependencies by `composer` 
````
-> composer install
-> php artisan migrate
````
It will create `people` table in database

## Environment Setup

Add bellow lines in `.env` file

````
PEOPLE_DATA_LAB_ENRICH_URL=https://api.peopledatalabs.com/v5/person/enrich
PEOPLE_DATA_LAB_API_KEY={USER_API_TOKEN}
````

## Environment Setup

Put the `csv` files in `public/csv` folder  
**Remove the headers in the csv file**



## CSV Format

| Email      | First Name | Last Name     |
| :---        |    :----:   |          ---: |
| abc@example.com      | John       | Doe   |

**When You put the files in `public/csv` remove the headers**

## Get Data

Run those two command 
````
-> php artisan sync:csv-to-db
-> php artisan sync:people-data-lab
````
