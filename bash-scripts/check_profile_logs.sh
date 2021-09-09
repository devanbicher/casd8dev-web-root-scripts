#!/bin/bash

curdir=$(pwd)

for sitedir in /var/www/casdev/web/sites/*/; do
    cd $sitedir

    if [ -d "profiles/cas_department" ]; then
        site=$(pwd | cut -d'/' -f7)
        cd profiles/cas_department
        echo $site

        # might have to find an elegant way to pipe output from fetching so it doesn't clutter up the output
        #but if you just run this script twice the second time the output will be 'pretty'
        git fetch
        git status | head -n2
        git log -n1 --pretty='Department Install Profile Commit: %cn on %cD  Message: %s'
        echo ""

    fi

done

cd /var/www/casdev/web/profiles/cas_department/
echo "Global install profile:  "
echo "(In /var/www/casdev/web/profiles/cas_department/)"

git fetch
git status | head -n2
git log -n1 --pretty='Department Install Profile Commit: %cn on %cD  Message: %s'

cd $curdir