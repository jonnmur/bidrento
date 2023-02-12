## Setup

1. Clone repository `git clone git@github.com:jonnmur/bidrento.git`.
2. Run `composer install`.
3. Modify `.env` with correct database parameters.
4. Run `php artisan key:generate`.
5. Run `php artisan migrate`.
6. Run `npm install`.
7. Run `npm run development`.

## Usage

1. Run `php artisan serve` to start the PHP development server.
2. Development server should start at `http://localhost:8000`, so open in browser: `http://localhost:8000`.
3. If you would like to test the API then make requests to these endpoints:

#### GET - Get properties tree
`http://localhost:8000/api/property`
#### GET - Get one property
`http://localhost:8000/api/property/{property-name}`
#### POST - Add new property
`http://localhost:8000/api/property`
+ Example payload:
```json
{
    "name": "Hotel 1",
    "parents": [4, 5],
    "children": [11, 14]
}
```

## Created files for this assignment

+ /app/Http/Controllers/Property/NodeController.php
+ /app/Http/Resources/Property/NodeResource.php
+ /app/Models/Property/Edge.php
+ /app/Models/Property/Node.php
+ /tests/Feature/Property/NodeTest.php
+ /database/migrations/2023_02_07_152530_create_edges_table.php
+ /database/migrations/2023_02_07_152530_create_nodes_table.php
+ /resources/js/app.js
+ /resources/js/Main.js
+ /resources/js/components/Add.js
+ /resources/js/components/PropertiesList.js
+ /resources/js/components/Property.js

## Modified files for this assignment

+ /routes/api.php
+ /config/database.php
+ /phpunit.xml

## Testing

+ Run `php artisan test` or `./vendor/bin/phpunit --testdox`.

## Assignment information

The task:
The goal of the test task is to create a service that stores and shows the organization of rental properties (parent/child/sibling).

Example of rental properties:
+ — Building complex
+ — — Building 1
+ — — — Parking space 1
+ — — Building 2
+ — — — Parking space 4
+ — — — Shared parking space 1
+ — — Building 3
+ — — — Shared parking space 1
+ — — — Parking space 8

Where: All property names are unique, parking spaces can be shared between Buildings (one parking space used by tenants from different buildings).

DOD:

REST API endpoint to retrieve full property tree
REST API endpoint to retrieve selected property (for example, building) with all its children, parents, siblings (For “Shared parking space 1” parents are “Building 2" and “Building 3”, siblings are “Parking space 4" and “Parking space 8” and no children). Returned structure should be “flat” sorted by property name, for example: [{“property”:“Building 1",“relation”:“parent”},{“property”:“Building 2",“relation”:“parent”},{“property”:“Parking space 4",“relation”:“ sibling”},{“property”:“Parking space 8",“relation”:“ sibling”},{“property”:“Shared parking space 1",“relation”:null}]
REST API endpoint to add a new property to any level of a tree.
Basic web app to display data requested via API.

Requirements:

Backend programming language: PHP
Database: MySQL
Frontend programming language: React
Tests are optional, but very welcomed

## Extra notes

+ Node version: v18.13.0
+ React version: 17.0.2
+ PHP version: 7.4.6
+ Laravel version: 8.75

## Examples

![example-tree](https://user-images.githubusercontent.com/28568108/218317237-54d51c71-ccd9-44b5-b4fe-0a1b33529797.png)
