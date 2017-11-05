<ul>
<?php
$reqPdo = class_exists('PDO', false);
$pdoDrv = $reqPdo ? PDO::getAvailableDrivers(): [];
$reqSqlite = in_array('sqlite', $pdoDrv);
$reqJson = function_exists('json_encode');
$reqRewrite = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true;
$reqOuterContent = ((function_exists('file_get_contents') && function_exists('ini_get') && ini_get('allow_url_fopen')) || function_exists('curl_init')) ? true : false;
$reqZip = class_exists('ZipArchive', false);

$errors = version_compare(PHP_VERSION, '5.6.0') < 0 ? '<li>php older than 5.6</li>' : '';
$errors .= !$reqPdo     ? '<li>pdo not supported</li>' : '';
$errors .= !$reqSqlite  ? '<li>sqlite driver for PDO not found</li>' : '';
$errors .= !$reqJson    ? '<li>json not supported</li>' : '';
$errors .= !$reqRewrite ? '<li>mod_rewrite not supported</li>' : '';
$errors .= !$reqOuterContent ? '<li>Neither url_fopen nor cURL is available</li>' : '';
$errors .= !$reqZip     ? '<li>ZipArchive not found</li>' : '';

if ($errors) {
    echo $errors;
} else {
    echo '<li>Server is OK!</li>';
}
?>
</ul>
