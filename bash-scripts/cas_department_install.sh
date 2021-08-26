#!/bin/bash

#save the current path so I can return to it
curpath=$(pwd)

# Get the git log for the install profile. 
# first check if there is a local install profile for that site.
cd /var/www/casdev/web/sites/"$1"
if [ -d "profiles/cas_department" ]; then
    echo "Using local install profile for $1 hopefully it is up to date ... "

    cd profiles/cas_department
    deptlog=$(git log -n1 --pretty='Department Install Profile Commit: %cn on %cD  Message: %s')   
    echo "most recent commit details for local Install profile: "
    echo $deptlog
    echo "Cancel now or use that install profile version ... "
else
    echo "No install profile in $1  Using global install profile."
    cd /var/www/casdev/web/profiles/cas_department/
    deptlog=$(git log -n1 --pretty='Department Install Profile Commit: %cn on %cD  Message: %s')
    echo "Here's the global install profile commit details, just to be safe: "
    echo $deptlog
fi

#go to the config folder
cd /var/www/casdev/web/files/"$1"/config

#check to make sure that git is in here, I've been burned by this before (it makes a commit in the drupal docroot, not the end of the world just annoying.)
if [ ! -d ".git" ]; then
    git init
    echo ".htaccess" > .gitignore
    git add .gitignore
    git commit -am "Initial Commit. only added the .gitignore file to start."
fi

#change permissions on .git to be able to run this stuff.
sudo chgrp -R drupaladm .git
sudo chmod g+xws -R .git

#make a commit of the config, in case there is somethig there you want.
git add ./*.yml
git commit -am "commit before exporting config again, before restoring to default profile."
#dump the config to make sure everthing from the site is exported and 'saved'
drush @casdev."$1" -y config:export 
git add ./*.yml
git commit -am "Most recent config for the site, BEFORE the site overwrite/install"
### NOW install/overwrite the site.
drush @casdev."$1" site-install cas_department --account-name="$1"_cas_admin --account-mail=incasweb@lehigh.edu --site-mail=incasweb@lehigh.edu --account-pass=$(pwgen 16) --site-name="CASDEV $1 (casd8dev)"
#export the config for the newly installed site.
drush @casdev."$1" -y config:export
git add ./*.yml
git commit -am "AFTER Resetting to cas_department install profile:  $deptlog"

cd $curpath