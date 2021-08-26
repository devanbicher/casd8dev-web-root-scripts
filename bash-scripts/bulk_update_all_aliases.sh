#!/bin/bash

cd /var/www/casdev/web/sites/

for site in /var/www/casdev/web/sites/*/; do
    onesite=$(echo "$site" | cut -d'/' -f7)
    echo "$onesite" "scr scripts/php-scripts/update_site_url_aliases.php"
    drush @casdev."$onesite" scr scripts/php-scripts/update_site_url_aliases.php
    echo "

    "
done