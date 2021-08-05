#!/bin/bash

#do this all in a temporary folder?

#function with command to strip out the uuid stuff.

site1="$1"
#check it exists, exists in sites

site2="$2"
#check it exists, exists in sites

docroot="/var/www/casdev/web/files"

site1config="$docroot"/"$site1"/config

mkdir -p /tmp/"$site1"/config/

echo "copying files from $site1config ... "
for config in "$docroot"/"$site1"/config/*.yml;do

	configfile=$(echo "$config" | cut -d'/' -f9) 
	# echo "Copying $configfile ... "

	pcregrep -vM '_core:(.*\n)[[:space:]]+default_config_hash:' $config | grep -v "uuid" > /tmp/"$site1"/config/"$configfile"
done    

site2config="$docroot"/"$site2"/config

mkdir -p /tmp/"$site2"/config/

echo "copying files from $site2config ... "
for config in "$docroot"/"$site2"/config/*.yml;do

	configfile=$(echo "$config" | cut -d'/' -f9) 
	# echo "Copying $configfile ... "

	pcregrep -vM '_core:(.*\n)[[:space:]]+default_config_hash:' $config | grep -v "uuid" > /tmp/"$site2"/config/"$configfile"
done    

diff -q /tmp/"$site1"/config/ /tmp/"$site2"/config/