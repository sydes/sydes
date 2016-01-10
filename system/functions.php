<?php

/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2016, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

use App\Http\Response;

/**
 * Print formatted array.
 *
 * @param array $array
 * @param bool  $return
 */
function pre($array, $return = false)
{
    $pre = '<pre>'.print_r($array, true).'</pre>';
    if ($return) {
        return $pre;
    }
    echo $pre;
}

/**
 * Find path to extension in default or custom folders.
 *
 * @param $type
 * @param $name
 * @return null|string
 */
function findExt($type, $name)
{
    $paths = [
        'module' => '/modules/'.$name,
        'iblock' => '/iblocks/'.$name,
        'plugin' => '/plugins/'.$name,
    ];

    foreach ([DIR_APP, DIR_SYSTEM] as $place) {
        $path = $place.$paths[$type];
        if (file_exists($path.'/index.php')) {
            return $path;
        }
    }
    return null;
}

/**
 * Load and execute file with given data.
 *
 * @param string $file
 * @param array  $result
 * @return string
 */
function render($file, $data = [])
{
    extract($data, EXTR_SKIP);
    ob_start();
    include $file;
    return ob_get_clean();
}

/**
 * Generate random string.
 *
 * @param int $length
 * @return null|string
 */
function token($length)
{
    $chars = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
        'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm',
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    if ($length < 0 || $length > 58){
        return null;
    }
    shuffle($chars);
    return implode('', array_slice($chars, 0, $length));
}

/**
 * Translate string.
 *
 * @param string $text
 * @return mixed
 */
function t($text)
{
    return app('translator')->translate($text);
}

/**
 * Make a slug from the string.
 *
 * @param string $str
 * @param bool   $strict
 * @return string
 */
function toSlug($str, $strict = true)
{
    $charsArray = [
        'a' => [
            'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
            'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
            'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
            'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
            'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ'],
        'b' => ['б', 'β', 'Ъ', 'Ь', 'ب'],
        'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ', 'ц'],
        'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
            'д', 'δ', 'د', 'ض'],
        'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
            'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
            'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
            'є', 'ə'],
        'f' => ['ф', 'φ', 'ف'],
        'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
        'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
        'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
            'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
            'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
            'ῗ', 'і', 'ї', 'и'],
        'j' => ['ĵ', 'ј', 'Ј'],
        'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
        'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
        'm' => ['м', 'μ', 'م'],
        'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
        'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
            'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
            'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
            'ö', 'о', 'و', 'θ'],
        'p' => ['п', 'π'],
        'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
        's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
        't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
        'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
            'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у'],
        'v' => ['в'],
        'w' => ['ŵ', 'ω', 'ώ'],
        'x' => ['χ'],
        'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
            'ϋ', 'ύ', 'ΰ', 'ي'],
        'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
        'aa' => ['ع'],
        'ae' => ['æ'],
        'ch' => ['ч'],
        'dj' => ['ђ', 'đ'],
        'dz' => ['џ'],
        'gh' => ['غ'],
        'kh' => ['х', 'خ'],
        'lj' => ['љ'],
        'nj' => ['њ'],
        'oe' => ['œ'],
        'ps' => ['ψ'],
        'sh' => ['ш'],
        'sch' => ['щ'],
        'ss' => ['ß'],
        'th' => ['þ', 'ث', 'ذ', 'ظ'],
        'ya' => ['я'],
        'yu' => ['ю'],
        'zh' => ['ж'],
        '(c)' => ['©'],
        'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
            'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
            'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
            'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
            'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А'],
        'B' => ['Б', 'Β'],
        'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ', 'Ц'],
        'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
        'E' => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
            'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
            'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
            'Є', 'Ə'],
        'F' => ['Ф', 'Φ'],
        'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
        'H' => ['Η', 'Ή'],
        'I' => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
            'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
            'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї'],
        'K' => ['К', 'Κ'],
        'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
        'M' => ['М', 'Μ'],
        'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
        'O' => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
            'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
            'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
            'Ὸ', 'Ό', 'О', 'Θ', 'Ө'],
        'P' => ['П', 'Π'],
        'R' => ['Ř', 'Ŕ', 'Р', 'Ρ'],
        'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
        'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
        'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
            'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У'],
        'V' => ['В'],
        'W' => ['Ω', 'Ώ'],
        'X' => ['Χ'],
        'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
            'Ы', 'Й', 'Υ', 'Ϋ'],
        'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
        'AE' => ['Æ'],
        'CH' => ['Ч'],
        'DJ' => ['Ђ'],
        'DZ' => ['Џ'],
        'KH' => ['Х'],
        'LJ' => ['Љ'],
        'NJ' => ['Њ'],
        'PS' => ['Ψ'],
        'SH' => ['Ш'],
        'SCH' => ['Щ'],
        'SS' => ['ẞ'],
        'TH' => ['Þ'],
        'YA' => ['Я'],
        'YU' => ['Ю'],
        'ZH' => ['Ж'],
        ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
            "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
            "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
            "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
            "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"],
        '-' => ['*', '+'],
    ];

    foreach ($charsArray as $key => $value) {
        $str = str_replace($value, $key, $str);
    }
    $add = $strict ? '' : '\./';
    $str = preg_replace('/[^\x20-\x7E]/u', '', $str);
    $str = preg_replace('![_]+!u', '-', $str);
    $str = preg_replace('![^\pL\pN\s'.$add.'-]+!u', '', mb_strtolower($str));
    $str = preg_replace('![\s-]+!u', '-', $str);

    return trim($str, '-');
}

/**
 * Check server for system requirements.
 *
 * @return array
 */
function checkServer()
{
    $wr = '';
    foreach (['app', 'themes/default', 'upload/images', 'upload/files', 'upload/_thumbs/Images', 'upload/_thumbs/Files'] as $path) {
        if (!is_writable(DIR_ROOT.'/'.$path)) {
            $wr .= "<li>{$path}</li>";
        }
    }
    if (!empty($wr)) {
        $wr = 'These folder is not writable: <ul>'.$wr.'</ul>';
    }

    $reqPdo = class_exists('PDO', false);
    $pdoDrv = $reqPdo ? PDO::getAvailableDrivers() : [];
    $reqSqlite = in_array('sqlite', $pdoDrv);
    $reqJson = function_exists('json_encode');
    $reqRewrite = function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : true;
    $reqOuterContent = ((function_exists('file_get_contents') && function_exists('ini_get') && ini_get('allow_url_fopen'))
        || function_exists('curl_init')) ? true : false;
    $reqZip = class_exists('ZipArchive', false);

    $errors = version_compare(PHP_VERSION, '5.4.0') < 0 ? '<li>php older than 5.4</li>' : '';
    $errors .= !$reqPdo ? '<li>PDO not supported</li>' : '';
    $errors .= !$reqSqlite ? '<li>SQLite driver for PDO not found</li>' : '';
    $errors .= !$reqJson ? '<li>Json not supported</li>' : '';
    $errors .= !$reqRewrite ? '<li>mod_rewrite not supported</li>' : '';
    $errors .= !$reqOuterContent ? '<li>Neither url_fopen nor cURL is available</li>' : '';
    $errors .= !$reqZip ? '<li>ZipArchive not found</li>' : '';

    if (!empty($errors)) {
        $errors = 'Server is not supported: <ul>'.$errors.'</ul>';
    }

    return [$errors, $wr];
}

/**
 * Print array to file for include.
 *
 * @param array  $array
 * @param string $filename
 */
function arr2file($array, $filename)
{
    $string = '<?php return '.var_export($array, true).';';
    file_put_contents($filename, $string, LOCK_EX);
    chmod($filename, 0777);
}

function getContentByUrl($url)
{
    $data = null;
    if (ini_get('allow_url_fopen')) {
        $data = file_get_contents($url);
    } elseif (function_exists('curl_init')) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
    }

    return $data;
}

function extractOuterZip($destination, $archive)
{
    $temp = DIR_TEMP.'/'.token(6);
    file_put_contents($temp, getContentByUrl($archive));

    $zip = new ZipArchive;
    if ($zip->open($temp) === true) {
        $zip->extractTo($destination);
        $zip->close();
        unlink($temp);
    }
}

/**
 * Get the available container instance.
 *
 * @param  string $key
 * @return mixed|App\App
 */
function app($key = null)
{
    if (is_null($key)) {
        return App\App::getInstance();
    }
    return App\App::getInstance()[$key];
}

/**
 * Get the document instance.
 *
 * @return App\Document
 */
function document($data = [])
{
    return new App\Document($data);
}

/**
 * Throw an HttpException with the given data.
 *
 * @param int    $code
 * @param string $message
 * @throws App\Exception\HttpException
 */
function abort($code, $message = '')
{
    App\App::getInstance()->abort($code, $message);
}

/**
 * Get the response instance.
 *
 * @param string $content
 * @param int    $statusCode
 * @param array  $headers
 * @return Response
 */
function response($content = '', $statusCode = 200, $headers = [])
{
    return new Response($content, $statusCode, $headers);
}

/**
 * Create a new redirect response to the given url.
 *
 * @param string $to
 * @param int    $status
 * @return Response|array
 */
function redirect($to = '', $status = 301)
{
    return app('request')->isAjax ? ['redirect' => $to] : (new Response)->withRedirect($to, $status);
}

/**
 * Create a new redirect response to the previous location.
 *
 * @return Response
 */
function back()
{
    $to = ifsetor(app('request')->headers['REFERER'], '');
    return (new Response)->withRedirect($to);
}

/**
 * Refresh page after ajax request.
 *
 * @return array|null
 */
function refresh()
{
    return ['refresh' => 1];
}

/**
 * Sets a notify message.
 *
 * @param string $message
 * @param string $status Any of bootstrap alert statuses
 * @return array
 */
function notify($message, $status = 'success')
{
    $_SESSION['notify'] = [
        'message' => $message,
        'status'  => $status,
    ];
    return ['notify' => $_SESSION['notify']];
}

/**
 * Adds a alert message.
 *
 * @param string $message
 * @param string $status Any of bootstrap alert statuses
 * @return array
 */
function alert($message, $status = 'success')
{
    $_SESSION['alerts'][] = [
        'message' => $message,
        'status'  => $status,
    ];
    return ['alerts' => $_SESSION['alerts']];
}

/**
 * Creates or loads config for extension
 *
 * @param string $module
 * @return App\Config
 */
function config($module)
{
    return new App\Config($module, app('db'));
}

if (!function_exists('ifsetor')) {
    function ifsetor(&$value, $default = null)
    {
        return isset($value) ? $value : $default;
    }
}

/**
 * Escape HTML entities in a string.
 *
 * @param string $str
 * @return string
 */
function e($str)
{
    return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
}

/**
 * Write to log
 *
 * @param $string
 */
function elog($string)
{
    $string = htmlentities($string);
    $date = date('r');
    $ip = app('request')->ip;
    file_put_contents(DIR_LOG.'/'.date('Ym').'.log', "$date | $ip | $string\n", FILE_APPEND | LOCK_EX);
}

function restricted()
{
    if (!app('user')->isAdmin()) {
        alert(t('error_mastercode_needed'), 'warning');
        $to = ifsetor(app('request')->headers['REFERER'], 'admin');
        throw new \App\Exception\RedirectException($to);
    }
}

/**
 * Loads model of some module
 *
 * @param string $module String like module_name or module_name/model_name
 * @return mixed
 */
function model($module)
{
    $part = strpos($module, '/') !== false ? explode('/', $module) : [$module, $module];
    $file = findExt('module', $part[0]).'/models/'.$part[1].'.php';

    if (!file_exists($file)) {
        throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
    }

    include_once $file;
    $class = ucfirst($part[1]).'Model';

    return new $class();
}

/**
 * Loads view of some module
 *
 * @param string $template String like module_name/view_name
 * @param array  $data
 * @return string
 */
function view($template, $data = [])
{
    $part = explode('/', $template);
    if (count($part) != 2) {
        throw new \RuntimeException(t('error_view_argument'));
    }

    app('event')->trigger('before.render.partial', [$template, $data], $template);

    $file_override = DIR_THEME.'/'.app('config')['site']['theme'].'/module/'.$template.'.php';
    $file = findExt('module', $part[0]).'/views/'.$part[1].'.php';
    if (file_exists($file_override)) {
        $html = render($file_override, $data);
    } elseif (file_exists($file)) {
        $html = render($file, $data);
    } else {
        throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
    }

    app('event')->trigger('after.render.partial', [$html], $template);

    return $html;
}

function parseLayoutData($str)
{
    $data = [];
    if (preg_match_all('/@([a-z]+)\(([\w -]+)\)/', $str, $matches)) {
        foreach ($matches[1] as $i => $key) {
            $data[$key] = $matches[2][$i];
        }
    }
    return $data;
}

/**
 * @param string $file
 * @return mixed
 */
function parse_json_file($file)
{
    return json_decode(file_get_contents($file), true);
}
