<!--
id: readme
tags: ''
-->

# Composer Repository Constants

A framework for building private Packagist-like repositories by In the Loft Studios.

## Requirements

* You must install on a public server.
* The server must have Composer installed.
* PackagesResource should be hosted on GitHub.

{{ composer.install|raw }}

## Installing

* `{{ composer.create_project|raw }}`
* Configure the webroot as _./app/web/_
* Run `./app/bin/install.sh`
* Follow instructions for replacing configuration tokens.
* Run `./app/bin/perms` to set the config perms.
* `app/bin/rebuild.sh`
* Visit the repository URL, confirming it loads.

## Add a Package

PackagesResource are adding by creating a GithubReporter webhook.

* Navigate to the GithubReporter repository for the package you want to reportChanges.
* Add a webhook:
    * Set url to:  `https://{repository_url}/api/packages.php`
    * Content type: `application/json`
    * Set the Secret
    * Let me select individual events
        * Branch or tag creation
        * Branch or tag deletion
* `app/bin/on_cron.sh`
* PackagesResource will appear after the next cron run. To accelerate the process manually execute `app/bin/on_cron.php`

## Finish Installation

* Create a cronjob with the desired publish frequency, e.g.,
    ```
    */15 * * * 1-6 /PATH/TO/app/bin/on_cron.php
    ```

## Usage

* Visit the https://{repository_url} and follow usage instructions.

{{ funding|raw }}
