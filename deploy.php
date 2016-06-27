<?php

function print_entry($key, $value) {
    print "define('".$key."', '".$value."');\n";
}

print "<?php\n";

// HTTP hosting

$host="http://42.120.10.42:8080"; // be06

print_entry("HTTP_SERVER", $host."/admin/");
print_entry("HTTP_CATALOG", $host."/admin/");
print_entry('HTTP_IMAGE', 'http://42.120.10.42/images/');

print_entry('NOTIFY_SERVER_HOST', '42.120.41.77');
print_entry('NOTIFY_SERVER_PORT', '1055');

// DIR
$root="/home/cwang/dev/projects/gift/server/frontend/admin/trunk/src";
print_entry("DIR_APPLICATION", $root."/");
print_entry("DIR_SYSTEM", $root."/system/");
print_entry("DIR_DATABASE", $root."/system/database/");
print_entry("DIR_LANGUAGE", $root."/language/");
print_entry("DIR_TEMPLATE", $root."/view/template/");
print_entry("DIR_CONFIG", $root."/system/config/");
print_entry("DIR_IMAGE", $root."/image/");
print_entry("DIR_CACHE", $root."/system/cache/");
print_entry("DIR_DOWNLOAD", $root."/download/");
print_entry("DIR_LOGS", $root."/system/logs/");

// DB
print_entry('DB_DRIVER', 'mysql');
print_entry('DB_HOSTNAME', '10.241.117.74'); // be01
print_entry('DB_USERNAME', 'mysql_remoter');
print_entry('DB_PASSWORD', '123456');
print_entry('DB_DATABASE', 'gift');
print_entry('DB_PREFIX', '');


print "?>\n";


?>
