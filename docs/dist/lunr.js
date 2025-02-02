var lunrIndex = [{"id":"changelog","title":"Changelog","body":"All notable changes to this project will be documented in this file.\n\nThe format is based on [Keep a Changelog](https:\/\/keepachangelog.com\/en\/1.0.0\/),\nand this project adheres to [Semantic Versioning](https:\/\/semver.org\/spec\/v2.0.0.html).\n\n## [Unreleased]\n\n- Nothing to list"},{"id":"readme","title":"Composer Repository Framework","body":"A framework for building private Packagist-like repositories by In the Loft Studios.\n\n## Requirements\n\n* You must install on a public server.\n* The server must have Composer installed.\n* Packages should be hosted on GitHub.\n\n## Installing\n\n* `composer create-project aklump\/composer-repository-framework:^0.0 app --repository=\"{\\\"type\\\":\\\"github\\\",\\\"url\\\": \\\"https:\/\/github.com\/aklump\/composer-repository-framework\\\"}\"`\n* Configure the webroot as _.\/app\/web\/_\n* Run `.\/app\/bin\/install.sh`\n* Follow instructions for replacing configuration tokens.\n* Run `.\/app\/bin\/perms` to set the config perms.\n* Create a cronjob with the desired publish frequency, e.g.,\n    ```\n    *\/15 * * * 1-6 \/PATH\/TO\/ROOT\/bin\/on_cron.php\n    ```\n\n## Adding a Package\n\nPackages are adding by creating a Github webhook.\n\n* Navigate to the Github repository for the package you want to add.\n* Create a webhook:\n    * Set url to:  `https:\/\/{repository_url}\/api\/packages.php?secret={API_SECRET}`\n    * Set action to: `push`\n* Packages will appear after the next cron run. To accelerate the process manually execute `app\/bin\/on_cron.php`\n\n## Usage\n\n* Visit the https:\/\/{repository_url} and follow usage instructions.\n\n## Sponsor this project\n\n  \r\n&nbsp;github.com\n\n&nbsp;buymeacoffee.com\n\n  &nbsp;paypal.com"}]