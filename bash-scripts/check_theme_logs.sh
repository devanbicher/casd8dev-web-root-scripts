#!/bin/bash

curdir=$(pwd)

for sitedir in /var/www/casdev/web/sites/*/; do
    cd $sitedir

    if [ -d "themes/cas_base" ]; then
        site=$(pwd | cut -d'/' -f7)
        cd themes/cas_base
        echo $site
        
        # might have to find an elegant way to pipe output from fetching so it doesn't clutter up the output
        #but if you just run this script twice the second time the output will be 'pretty'
        git fetch
        git status | head -n2
        git log -n1 --pretty='CAS Base Theme Commit: %cn on %cD  Message: %s'
        echo ""

    fi

done

cd /var/www/casdev/web/themes/custom/cas_base
echo "Global CAS Base Theme:  "
echo "(In /var/www/casdev/web/themes/custom/cas_base/)"

git fetch
git status | head -n2
git log -n1 --pretty='CAS Base Theme Commit: %cn on %cD  Message: %s'


cd $curdir