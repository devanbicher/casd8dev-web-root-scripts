#!/bin/bash

for site in /var/www/casdev/web/sites/*/; do
    onesite=$(echo "$site" | cut -d'/' -f7)
    rightnow=$(date +'%H%M-%m%d%y')
    echo "drush @casdev.""$onesite sql:dump --result-file=files/""$onesite""/private/backup_migrate/""$onesite""-sh-""$rightnow"".sql --gzip --structure-tables-list=..."
    #sql:dump --result-file=files/"$site"/private/backup_migrate/"$site"-sh-$(date +'%H%M-%m%d%y').sql --gzip --structure-tables-list=cache_bootstrap,cache_config,cache_container,cache_data,cache_default,cache_discovery,cache_entity,cache_menu,cache_page,cache_toolbar,sessions
    drush @casdev."$onesite" sql:dump --result-file=files/"$onesite"/private/backup_migrate/"$onesite"-sh-"$rightnow".sql --gzip --structure-tables-list=cache_bootstrap,cache_config,cache_container,cache_data,cache_default,cache_discovery,cache_entity,cache_menu,cache_page,cache_toolbar,sessions
    echo "

    "
done