<!--
id: readme
tags: ''
-->

# Private Packagist for In the Loft Studios

This repository is for indexing unpublished Composer dependencies.

## Installation

1. Copy _./install/satis.json_ to _./satis.json_
   *  Open _.satis.json_ and set `name` (e.g., `aklump/packages`) and `homepage` (e.g., `https://packages.intheloftstudios.com`) to the public repository URL.
3. Copy _./install/config.sh_ to _./config.sh_
   * Open _./config.sh_ and set the values for SSH publishing.
5. Make sure _.cache_ is writeable
6. Create a cronjob with the desired publish frequency that executes app/bin/on_cron.sh, e.g.
    ```
    */15 * * * 1-6 /Users/aklump/Code/Projects/InTheLoftStudios/InTheLoftStudios/site-packages/app/bin/on_cron.sh
    ```
7. Create an easy-to-remember alias for triggering updates:
    * `mkdir -p ~/bin && cd ~/bin`
    * `ln -s /Users/aklump/Code/Projects/InTheLoftStudios/InTheLoftStudios/site-packages/app/bin/on_package_change.sh package_changed.sh`

## Adding a Package

1. Create an entry in _satis.json_
2. Any time a new version of this package is published execute `~/bin/package_changed.sh`

## Commands

These commands may be used, but are generally not necessary.

* `bin/rebuild.sh` Scans all packages for dependencies and builds the static dependency file.
* `bin/publish.sh` Push the existing dependency file to the live site.

