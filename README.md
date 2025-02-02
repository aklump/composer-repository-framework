# Composer Repository Framework

A framework for building private Packagist-like repositories by In the Loft Studios.

```shell

```

## Requirements

* You must install on a public server.
* The server must have Composer installed.

## Installing

* `composer create-project aklump/composer-repository-framework:@dev --repository="{\"type\":\"github\",\"url\": \"https://github.com/aklump/composer-repository-framework\"}"`
* Configure the webroot as _./web/_
* Run `./bin/install.sh`
* Follow instructions for replacing configuration tokens.
* Create a cronjob with the desired publish frequency, e.g.,
    ```
    */15 * * * 1-6 /PATH/TO/ROOT/bin/on_cron.php
    ```

## Adding a Package

Packages are adding by creating a Github webhook.

* Navigate to the Github repository for the package you want to add.

Create a github hook to this url `https://{repository_url}/api/event.php?secret={API_SECRET}` for the `push` action.

## Commands

These commands may be used, but are generally not necessary.

* `bin/rebuild.sh` Scans all packages for dependencies and builds the static dependency file.
* `bin/publish.sh` or `bin/publish.sh -v` Push the existing dependency file to the repository server.
