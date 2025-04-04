<?php
define('HTTP_CATALOG', getenv('HTTP_SERVER') ?: 'http://localhost/');
define('HTTPS_CATALOG', getenv('HTTPS_SERVER') ?: 'http://localhost/');

$adminPath = getenv('ADMIN_PATH') ?: 'admin/';
define('HTTP_SERVER', HTTP_CATALOG . $adminPath);
define('HTTPS_SERVER', HTTPS_CATALOG . $adminPath);


$baseDir = getenv('DIR_BASE') ?: '/var/www/html';
$storageDir = getenv('DIR_STORAGE') ?: '/var/www/storage';

define('DIR_APPLICATION', $baseDir . '/admin/');
define('DIR_SYSTEM', $baseDir . '/system/');
define('DIR_IMAGE', $baseDir . '/image/');
define('DIR_STORAGE', $storageDir . '/');
define('DIR_CATALOG', $baseDir . '/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

define('DB_DRIVER', getenv('DB_DRIVER'));
define('DB_HOSTNAME', getenv('DB_HOSTNAME'));
define('DB_USERNAME', getenv('DB_USER_NAME'));
define('DB_PASSWORD', getenv('DB_USER_PASSWORD'));
define('DB_DATABASE', getenv('DB_DATABASE'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_PREFIX', getenv('DB_PREFIX'));

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
define('OPENCARTFORUM_SERVER', 'https://opencartforum.com/');
