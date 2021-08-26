#!/bin/bash

#this is being run as a cronjob every day at midnight.

#get the most recent commit message from the cas_department install profile
cd /var/www/casdev/web/profiles/cas_department/
deptlog=$(git log -n1 --pretty='Department Install Profile Commit: %cn on %cD  Message: %s')

#Go to the casdev doc root to do stuff with the department base install
cd /var/www/casdev/web/files/cas_department_base/config

#install the site
/usr/local/bin/drush @casdev.cas_department_base -y site-install cas_department --account-name=cas_department_base_admin --account-mail=incasweb@lehigh.edu --site-mail=incasweb@lehigh.edu --account-pass=$(pwgen 16) --site-name="CAS Department Base (casd8dev)"

#export the config for the newly installed site.
/usr/local/bin/drush @casdev.cas_department_base -y config:export

#track it with git.
git add ./*.yml
git commit -am "$deptlog"
git push origin master
