#!/bin/bash
_now=$(date +"%Y%m%d")
_file="/home/ubuntu/chef/data/chef_db_$_now.sql"
echo "Start to backup chef db to $_file..."
mysqldump --databases chef --user=root --password=chef2015L > "$_file"
