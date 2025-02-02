# Composer Repository Framework

A framework for building private Packagist-like repositories by In the Loft Studios.

## Requirements

* You must install on a public server.
* The server must have Composer installed.
* Packages should be hosted on GitHub.

## Installing

* `composer create-project aklump/composer-repository-framework:^0.0 app --repository="{\"type\":\"github\",\"url\": \"https://github.com/aklump/composer-repository-framework\"}"`
* Configure the webroot as _./app/web/_
* Run `./app/bin/install.sh`
* Follow instructions for replacing configuration tokens.
* Run `./app/bin/perms` to set the config perms.
* Create a cronjob with the desired publish frequency, e.g.,
    ```
    */15 * * * 1-6 /PATH/TO/ROOT/bin/on_cron.php
    ```

## Adding a Package

Packages are adding by creating a Github webhook.

* Navigate to the Github repository for the package you want to add.
* Create a webhook:
    * Set url to:  `https://{repository_url}/api/packages.php?secret={API_SECRET}`
    * Set action to: `push`
* Packages will appear after the next cron run. To accelerate the process manually execute `app/bin/on_cron.php`

## Usage

* Visit the https://{repository_url} and follow usage instructions.
