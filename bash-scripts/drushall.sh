#!/bin/bash

for site in /var/www/casdev/web/sites/*/; do
    onesite=$(echo "$site" | cut -d'/' -f7)
    echo "$onesite" "$*"
    drush @casdev."$onesite" $*
    echo "

    "
done