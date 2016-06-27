#!/bin/bash                                                                                           

SOURCE=src
CONFIG=$SOURCE/config.php
TARGET=/var/www/html/customers
IMAGE_TARGET=/var/www/html/images
HOST="http://120.92.10.42/customers/"

set -x

# export code

EXPORT_CODE=0

if [ $EXPORT_CODE == 1 ]; then
    rm -rf $SOURCE
    svn export src $SOURCE
fi


# generate config

touch $CONFIG

echo "<?php " > $CONFIG

# http hosting

echo "define('HTTP_SERVER', '$HOST');" >> $CONFIG
echo "define('HTTP_CATALOG', '$HOST');" >> $CONFIG
echo "define('HTTP_IMAGE', 'http://42.120.48.230/images/');" >> $CONFIG
echo "define('LOCAL_IMAGE', 'http://uc01.weizhi1.com/images/');" >> $CONFIG
echo "define('NOTIFY_SERVER_HOST', '42.120.41.77');" >> $CONFIG
echo "define('NOTIFY_SERVER_PORT', '1055');" >> $CONFIG

# DIR
echo "define('DIR_APPLICATION', '$TARGET/');" >> $CONFIG
echo "define('DIR_SYSTEM', '$TARGET/system/');" >> $CONFIG
echo "define('DIR_DATABASE', '$TARGET/system/database/');" >> $CONFIG
echo "define('DIR_LANGUAGE', '$TARGET/language/');" >> $CONFIG
echo "define('DIR_TEMPLATE', '$TARGET/view/template/');" >> $CONFIG
echo "define('DIR_CONFIG', '$TARGET/system/config/');" >> $CONFIG
echo "define('DIR_IMAGE', '$IMAGE_TARGET/');" >> $CONFIG
echo "define('DIR_CACHE', '$TARGET/system/cache/');" >> $CONFIG
echo "define('DIR_DOWNLOAD', '$TARGET/download/');" >> $CONFIG
echo "define('DIR_LOGS', '$TARGET/system/logs/');" >> $CONFIG

# DB
echo "define('DB_DRIVER', 'mysql');" >> $CONFIG
echo "define('DB_HOSTNAME', 'localhost');" >> $CONFIG
echo "define('DB_USERNAME', 'root');" >> $CONFIG
echo "define('DB_PASSWORD', 'chef2015L');" >> $CONFIG
echo "define('DB_DATABASE', 'chef');" >> $CONFIG
echo "define('DB_PREFIX', 'sc_');" >> $CONFIG

echo "?>" >> $CONFIG

# clear cache
sudo  rm -rf $TARGET/system/cache/

# copy
sudo mkdir -p $TARGET

sudo cp -rf $SOURCE/* $TARGET

sudo chgrp -R www-data $TARGET/system/cache
sudo chmod -R g+w $TARGET/system/cache

sudo chgrp -R www-data $TARGET/image/cache
sudo chmod -R g+w $TARGET/image/cache

sudo chgrp -R www-data $TARGET/download
sudo chmod -R g+w $TARGET/download

sudo mkdir $TARGET/system/logs
sudo chgrp -R www-data $TARGET/system/logs
sudo chmod -R g+w $TARGET/system/logs

