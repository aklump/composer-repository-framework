# Private Packagist for In the Loft Studios

This repository is for indexing unpublished Composer dependencies.

## Installation

1. Create a public server and domain to act as the `repository` entry in _composer.json_ files of the depending projects.
2. Configure an SSH user on the server for publishing changes to the repository.
5. Copy _./install/config.sh_ to _./config.sh_; open _./config.sh_ and edit:
    * Set `REPOSITORY_URL`, e.g. `https://packages.intheloftstudios.com`
    * Set the SSH info for connecting to the repositry server.
4. Copy _./install/satis.json_ to _./satis.json_
    * Open _.satis.json_ and set `name` (e.g., `aklump/packages`) and `homepage`, which should match `REPOSITORY_URL` in _config.sh_.
6. Make sure _.cache/_ and _web/_ directories are both writeable.
7. Create a cronjob with the desired publish frequency that executes app/bin/on_cron.sh, e.g.
    ```
    */15 * * * 1-6 /Users/aklump/Code/Projects/InTheLoftStudios/InTheLoftStudios/site-packages/app/bin/on_cron.sh
    ```
8. Create an easy-to-remember alias for triggering updates:
    * `mkdir -p ~/bin && cd ~/bin`
    * `ln -s /Users/aklump/Code/Projects/InTheLoftStudios/InTheLoftStudios/site-packages/app/bin/on_package_change.sh package_changed.sh`

## Adding a Package

1. Create an entry in _satis.json_
2. Any time a new version of this package is published execute `~/bin/package_changed.sh`

## Commands

These commands may be used, but are generally not necessary.

* `bin/rebuild.sh` Scans all packages for dependencies and builds the static dependency file.
* `bin/publish.sh` Push the existing dependency file to the live site.
