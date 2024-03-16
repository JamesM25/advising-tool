# Project Files and Structure
This document describes the structure of the project, and the purpose of project files and directories.

## controller/Controller.php
This file defines routes for each page and API endpoint.

## db/db.sql
This file contains all of the SQL `CREATE TABLE ...` statements to initialize a MySQL database, and insert default data.

## documentation/
This directory contains markdown documents describing how the project functions, and how to use it.

## images/
Contains images used in the application itself.

## model/
Contains data objects and business logic used in the application.

### model/AI.php
Interacts with OpenAI's API to make further adjustments to generated schedule data.

In order for this functionality to work, a configuration file containing an OpenAI API key must be created above the root directory of the project (see [model/DataLayer.php](#configuration-file) for more info).

This file must be called `openai_key.php` and contain the following
```php
<?php
define("OPENAI_API_KEY", "your_key_here");
```

`your_key_here` must be replaced with an actual API key. These may be found [here](https://platform.openai.com/api-keys).

### model/DataLayer.php
Interacts with the database.

#### Configuration file
In order for this file to function properly, a configuration file must be placed above the root directory of the project.
In our deployment, the project is located within a directory called `public_html` inside a user folder,
e.g. `/home/james/public_html/`.
In this case, the configuration file should be placed in `/home/james/`, e.g. `/home/james/advising_db.php`

The purpose of storing the file above the `public_html` directory is to ensure that it is never accessible from the web,
as the file contains database access credentials.

### model/Quarter.php
Represents the year and season corresponding with an academic quarter.

### model/Schedule.php
Implements business logic to generate academic plans.

### model/StudentForm.php
Stores data retrieved from the student form submitted before schedule generation.

## scripts/admin.js
Contains all client-side logic for the admin page,
sends requests to the API to update course data according to user input.

## styles/
Contains CSS styles used throughout the application.

## view/
Contains HTML documents used to render pages in the browser.

### view/includes/
Contains HTML files that are included in other files.