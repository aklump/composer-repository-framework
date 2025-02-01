# Private Packagist for In the Loft Studios

This repository is for indexing unpublished Composer dependencies.

## Installation

1. Create a public server and domain to act as the `repository` entry in _composer.json_ files of the depending projects.
2. Configure an SSH user on the server for publishing changes to the repository.
3. Setup key-based authentication for publishing to the repository server.
4. Copy _./install/config.sh_ to _./config.sh_; open _./config.sh_ and edit:
    * Set `REPOSITORY_URL`, e.g. `https://packages.intheloftstudios.com`
    * Set the SSH info for connecting to the repositry server.
5. Copy _./install/satis.json_ to _./satis.json_
    * Open _.satis.json_ and set `name` (e.g., `aklump/packages`) and `homepage`, which should match `REPOSITORY_URL` in _config.sh_.
6. Copy _./install/.env_ to _web/.env_ and enter a strong value for `API_SECRET`.
7. Make sure _.cache/_ and _web/_ directories are both writeable.
8. Create a cronjob with the desired publish frequency that executes app/bin/on_cron.sh, e.g.
    ```
    */15 * * * 1-6 /Users/aklump/Code/Projects/InTheLoftStudios/InTheLoftStudios/site-packages/app/bin/on_cron.sh
    ```

## Adding a Package

Create a github hook to this url `https://packages.intheloftstudios.com/api/event.php?secret={API_SECRET}` for the `push` action.

## Commands

These commands may be used, but are generally not necessary.

* `bin/rebuild.sh` Scans all packages for dependencies and builds the static dependency file.
* `bin/publish.sh` or `bin/publish.sh -v` Push the existing dependency file to the repository server.
