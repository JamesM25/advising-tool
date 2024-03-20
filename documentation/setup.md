# Setup

This project was developed within a LAMP (Linux, Apache, MySQL, PHP) environment. While it may be possible to deploy
the application within a different environment, this document will assume that a LAMP environment is used.

## Database
This project requires access to a MySQL database in order to store course and prerequisite data.
A script is provided at `db/db.sql` to initialize the database tables and insert default data.

Once the database is initialized, a database user should be created with the following permissions:

* INSERT
* SELECT
* UPDATE
* DELETE

After the database is ready, a configuration file named `db_advising.php` should be placed outside the directory containing the project,
such that the PHP expression `$_SERVER['DOCUMENT_ROOT'] . '/../db_advising.php'` corresponds with the file's path.

During development, the project was located in a directory called `public_html`, which exists inside a user directory, e.g. `/home/james/public_html`.
In this case, the configuration file should be placed above the `public_html` directory, e.g. `/home/james/db_advising.php`.

The configuration file should contain the following:
```php
<?php
define( "DB_DSN", "mysql:dbname=db_name_here" );
define( "DB_USERNAME", "db_username_here" );
define( "DB_PASSWORD", "db_password_here" );
```
With `db_name_here` replaced with the name of your database,
and `db_username_here` & `db_password_here` replaced with the credentials corresponding with the user that was created earlier.

The purpose of storing this file outside of the project directory is to ensure that it is not accessible from the web, to prevent the leak of possibly sensitive credentials.

## OpenAI API
In order to use the AI functionality during schedule generation, an OpenAI API key must be provided.
To do this, create a file called `openai_key.php`,
at the same location as the database configuration file described in the database section.

The contents of `openai_key.php` should be as follows:
```php
<?php
define("OPENAI_API_KEY", "your_key_here")
```
With `your_key_here` replaced with an actual API key. These may be found [here](https://platform.openai.com/api-keys).

As of the time of writing, the AI functionality is configured to use OpenAI's `gpt-3.5-turbo-0125` model, but this may be adjusted in `model/AI.php`.

