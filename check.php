<ul>
<?php
$req_pdo = class_exists('PDO', false);
$pdo_drv = $req_pdo ? PDO::getAvailableDrivers(): array();
$req_sqlite = in_array('sqlite', $pdo_drv);
$req_json = function_exists('json_encode');
$req_rewrite = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true;
$req_outer_content = ((function_exists('file_get_contents') && function_exists('ini_get') && ini_get('allow_url_fopen')) || function_exists('curl_init')) ? true : false;
$req_zip = class_exists('ZipArchive', false);

$errors = version_compare(PHP_VERSION, '5.3.0') < 0 ? '<li>php older than 5.3</li>' : '';
$errors .= !$req_pdo     ? '<li>pdo not supported</li>' : '';
$errors .= !$req_sqlite  ? '<li>sqlite driver for pdo not found</li>' : '';
$errors .= !$req_json    ? '<li>json not supported</li>' : '';
$errors .= !$req_rewrite ? '<li>mod_rewrite not supported</li>' : '';
$errors .= !$req_outer_content ? '<li>Neither url_fopen nor cURL is available</li>' : '';
$errors .= !$req_zip     ? '<li>ZipArchive not found</li>' : '';

if ($errors){
	echo $errors;
} else {
	echo 'Server is OK!';
}
?>
</ul>
