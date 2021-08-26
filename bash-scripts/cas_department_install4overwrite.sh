#!/bin/bash

# to speed this up I removed everything from the cas_department_install script.  Add that back in after testing.
drush @casdev."$1" -y site-install cas_department --account-name="$1"_cas_admin --account-mail=incasweb@lehigh.edu --site-mail=incasweb@lehigh.edu --account-pass=$(pwgen 16) --site-name="CASDEV $1 (casd8dev)"
