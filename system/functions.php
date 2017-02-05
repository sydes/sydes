<?php
/**
 * SyDES - Lightweight CMF for a simple sites with SQLite database
 *
 * @package   SyDES
 * @copyright 2011-2017, ArtyGrand <artygrand.ru>
 * @license   GNU GPL v3 or later; see LICENSE
 */

use App\Container;
use App\Exception\AppException;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;

/**
 * Print formatted array.
 *
 * @param array|object $array
 * @param bool  $return
 *
 * @return string|bool
 */
function pre($array, $return = false)
{
    $pre = '<pre>'.print_r($array, true).'</pre>';
    if ($return) {
        return $pre;
    }
    echo $pre;
    return true;
}

/**
 * Find path to infoBlock in theme or app.
 *
 * @param string $name
 * @return null|string
 */
function iblockDir($name)
{
    $places = [DIR_THEME.'/'.app('site')['theme'], DIR_APP];

    foreach ($places as $place) {
        $path = $place.'/iblocks/'.$name;
        if (file_exists($path.'/iblock.php')) {
            return $path;
        }
    }

    foreach (app('site')['modules'] as $modName => $module) {
        if (isset($module['iblocks']) && in_array($name, $module['iblocks'])) {
            return moduleDir($modName).'/iblocks/'.$name;
        }
    }

    return null;
}

/**
 * Find path to module in core or user folders.
 *
 * @param string $name
 * @return null|string
 */
function moduleDir($name)
{
    $name = studly_case($name);

    foreach ([DIR_APP, DIR_SYSTEM] as $place) {
        $path = $place.'/modules/'.$name;
        if (file_exists($path.'/Controller.php')) {
            return $path;
        }
    }
    return null;
}

function assetsDir($module)
{
    return str_replace(DIR_ROOT, '', moduleDir($module)).'/assets';
}

/**
 * Load and execute file with given data.
 *
 * @param string $file
 * @param array  $data
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
    $chars = ['A','B','C','D','E','F','G','H','J','K','L','M','N','O','P','Q','R',
        'S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h',
        'i','j','k','m','n','o','p','q','r','s','t','u','v','w','x','y',
        'z','1','2','3','4','5','6','7','8','9',];
    if ($length < 0 || $length > 58) {
        $length = 16;
    }
    shuffle($chars);
    return implode('', array_slice($chars, 0, $length));
}

/**
 * Get the available container instance.
 *
 * @param  string $id
 * @return mixed|Container
 */
function app($id = null)
{
    if (is_null($id)) {
        return Container::getContainer();
    }

    return Container::getContainer()[$id];
}

/**
 * Translate string.
 *
 * @param string $text
 * @return string
 */
function t($text)
{
    return app('translator')->translate($text);
}

/**
 * Pluralize translated string.
 *
 * @param string $text
 * @return string
 */
function p($text, $count, $context = [])
{
    return app('translator')->pluralize($text, $count, $context);
}

/**
 * @param string $text
 * @param array  $context
 * @return string
 */
function interpolate($text, array $context = [])
{
    if (false === strpos($text, '{') || empty($context)) {
        return $text;
    }

    $replace = [];
    foreach ($context as $key => $val) {
        if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
            $replace['{'.$key.'}'] = $val;
        } elseif (is_object($val)) {
            $replace['{'.$key.'}'] = '[object '.get_class($val).']';
        } else {
            $replace['{'.$key.'}'] = '['.gettype($val).']';
        }
    }

    return strtr($text, $replace);
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
        'a'   => ['à','á','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ',
            'ậ','ä','ā','ą','å','α','ά','ἀ','ἁ','ἂ','ἃ','ἄ','ἅ','ἆ','ἇ','ᾀ',
            'ᾁ','ᾂ','ᾃ','ᾄ','ᾅ','ᾆ','ᾇ','ὰ','ά','ᾰ','ᾱ','ᾲ','ᾳ','ᾴ','ᾶ','ᾷ','а','أ',],
        'b'   => ['б', 'β', 'Ъ', 'Ь', 'ب'],
        'c'   => ['ç', 'ć', 'č', 'ĉ', 'ċ', 'ц', '©'],
        'd'   => ['ď','ð','đ','ƌ','ȡ','ɖ','ɗ','ᵭ','ᶁ','ᶑ','д','δ','د','ض',],
        'e'   => ['é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ','ë','ē','ę','ě','ĕ','ė',
            'ε','έ','ἐ','ἑ','ἒ','ἓ','ἔ','ἕ','ὲ','έ','е','ё','э','є','ə',],
        'f'   => ['ф', 'φ', 'ف'],
        'g'   => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
        'h'   => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
        'i'   => ['í','ì','ỉ','ĩ','ị','î','ï','ī','ĭ','į','ı','ι','ί','ϊ','ΐ','ἰ',
            'ἱ','ἲ','ἳ','ἴ','ἵ','ἶ','ἷ','ὶ','ί','ῐ','ῑ','ῒ','ΐ','ῖ','ῗ','і','ї','и', ],
        'j'   => ['ĵ', 'ј', 'Ј'],
        'k'   => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
        'l'   => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
        'm'   => ['м', 'μ', 'م'],
        'n'   => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
        'o'   => ['ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ',
            'ợ','ø','ō','ő','ŏ','ο','ὀ','ὁ','ὂ','ὃ','ὄ','ὅ','ὸ','ό','ö','о','و','θ',],
        'p'   => ['п', 'π'],
        'r'   => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
        's'   => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
        't'   => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
        'u'   => ['ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự','ü','û','ū','ů','ű','ŭ','ų','µ','у',],
        'v'   => ['в'],
        'w'   => ['ŵ', 'ω', 'ώ'],
        'x'   => ['χ'],
        'y'   => ['ý','ỳ','ỷ','ỹ','ỵ','ÿ','ŷ','й','ы','υ','ϋ','ύ','ΰ','ي',],
        'z'   => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
        'aa'  => ['ع'],
        'ae'  => ['æ'],
        'ch'  => ['ч'],
        'dj'  => ['ђ', 'đ'],
        'dz'  => ['џ'],
        'gh'  => ['غ'],
        'kh'  => ['х', 'خ'],
        'lj'  => ['љ'],
        'nj'  => ['њ'],
        'oe'  => ['œ'],
        'ps'  => ['ψ'],
        'sh'  => ['ш'],
        'sch' => ['щ'],
        'ss'  => ['ß'],
        'th'  => ['þ', 'ث', 'ذ', 'ظ'],
        'ya'  => ['я'],
        'yu'  => ['ю'],
        'zh'  => ['ж'],
        'A'   => ['Á','À','Ả','Ã','Ạ','Ă','Ắ','Ằ','Ẳ','Ẵ','Ặ','Â','Ấ','Ầ','Ẩ','Ẫ',
            'Ậ','Ä','Å','Ā','Ą','Α','Ά','Ἀ','Ἁ','Ἂ','Ἃ','Ἄ','Ἅ','Ἆ','Ἇ','ᾈ',
            'ᾉ','ᾊ','ᾋ','ᾌ','ᾍ','ᾎ','ᾏ','Ᾰ','Ᾱ','Ὰ','Ά','ᾼ','А',],
        'B'   => ['Б', 'Β'],
        'C'   => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ', 'Ц'],
        'D'   => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
        'E'   => ['É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ','Ë','Ē','Ę','Ě','Ĕ',
            'Ė','Ε','Έ','Ἐ','Ἑ','Ἒ','Ἓ','Ἔ','Ἕ','Έ','Ὲ','Е','Ё','Э','Є','Ə',],
        'F'   => ['Ф', 'Φ'],
        'G'   => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
        'H'   => ['Η', 'Ή'],
        'I'   => ['Í','Ì','Ỉ','Ĩ','Ị','Î','Ï','Ī','Ĭ','Į','İ','Ι','Ί','Ϊ','Ἰ','Ἱ',
            'Ἳ','Ἴ','Ἵ','Ἶ','Ἷ','Ῐ','Ῑ','Ὶ','Ί','И','І','Ї',],
        'K'   => ['К', 'Κ'],
        'L'   => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
        'M'   => ['М', 'Μ'],
        'N'   => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
        'O'   => ['Ó','Ò','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ',
            'Ợ','Ö','Ø','Ō','Ő','Ŏ','Ο','Ό','Ὀ','Ὁ','Ὂ','Ὃ','Ὄ','Ὅ','Ὸ','Ό','О','Θ','Ө',],
        'P'   => ['П', 'Π'],
        'R'   => ['Ř', 'Ŕ', 'Р', 'Ρ'],
        'S'   => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
        'T'   => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
        'U'   => ['Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự','Û','Ü','Ū','Ů','Ű','Ŭ','Ų','У',],
        'V'   => ['В'],
        'W'   => ['Ω', 'Ώ'],
        'X'   => ['Χ'],
        'Y'   => ['Ý','Ỳ','Ỷ','Ỹ','Ỵ','Ÿ','Ῠ','Ῡ','Ὺ','Ύ','Ы','Й','Υ','Ϋ',],
        'Z'   => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
        'AE'  => ['Æ'],
        'CH'  => ['Ч'],
        'DJ'  => ['Ђ'],
        'DZ'  => ['Џ'],
        'KH'  => ['Х'],
        'LJ'  => ['Љ'],
        'NJ'  => ['Њ'],
        'PS'  => ['Ψ'],
        'SH'  => ['Ш'],
        'SCH' => ['Щ'],
        'SS'  => ['ẞ'],
        'TH'  => ['Þ'],
        'YA'  => ['Я'],
        'YU'  => ['Ю'],
        'ZH'  => ['Ж'],
        ' '   => [
            "\xC2\xA0",
            "\xE2\x80\x80",
            "\xE2\x80\x81",
            "\xE2\x80\x82",
            "\xE2\x80\x83",
            "\xE2\x80\x84",
            "\xE2\x80\x85",
            "\xE2\x80\x86",
            "\xE2\x80\x87",
            "\xE2\x80\x88",
            "\xE2\x80\x89",
            "\xE2\x80\x8A",
            "\xE2\x80\xAF",
            "\xE2\x81\x9F",
            "\xE3\x80\x80",
        ],
        '-'   => ['*', '+'],
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
    foreach (['upload/images','upload/files','upload/_thumbs/Images','upload/_thumbs/Files'] as $path) {
        if (!is_writable(DIR_ROOT.'/'.$path)) {
            $wr .= "<li>{$path}</li>";
        }
    }
    if (!empty($wr)) {
        $wr = 'These folder is not writable: <ul>'.$wr.'</ul>';
    }

    return $wr;
}

/**
 * Print array to file for include.
 *
 * @param array  $array
 * @param string $filename
 */
function array2file($array, $filename)
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

/**
 * @param $destination string Folder to unpack
 * @param $archive     string Url of archive
 * @return bool
 */
function extractOuterZip($destination, $archive)
{
    $result = false;
    $temp = DIR_TEMP.'/'.token(6);
    file_put_contents($temp, getContentByUrl($archive));

    $zip = new ZipArchive;
    if ($zip->open($temp) === true) {
        $zip->extractTo($destination);
        $zip->close();

        $result = true;
    }

    unlink($temp);
    return $result;
}

/**
 * Gets value from $_POST or $_GET or use default
 *
 * @param        $key
 * @param string $default
 * @return mixed
 */
function request($key, $default = null)
{
    $request = app('request');
    $postParams = $request->getParsedBody();
    $getParams = $request->getQueryParams();
    $result = $default;
    if (is_array($postParams) && isset($postParams[$key])) {
        $result = $postParams[$key];
    } elseif (isset($getParams[$key])) {
        $result = $getParams[$key];
    }

    return $result;
}

/**
 * @param int    $code
 * @param string $message
 * @throws AppException
 */
function abort($code, $message = '')
{
    throw new AppException($message, $code);
}

/**
 * Get the document instance.
 * @param array $data
 * @return \App\Document
 */
function document($data = [])
{
    return new App\Document($data);
}

/**
 * Get basic PSR-7 Response object
 *
 * @param string $content
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function response($content = '', $status = 200, array $headers = [])
{
    $res = new Response('php://memory', $status, $headers);
    $res->getBody()->write($content);

    return $res;
}

/**
 * Get PSR-7 Response object with Content-Type as plain text
 *
 * @param string $text
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function text($text, $status = 200, $headers = [])
{
    return new Response\TextResponse($text, $status, $headers);
}

/**
 * Get PSR-7 Response object with Content-Type as html
 *
 * @param string $html
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function html($html, $status = 200, $headers = [])
{
    return new Response\HtmlResponse($html, $status, $headers);
}

/**
 * Get PSR-7 Response object without body
 *
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function head($status = 204, $headers = [])
{
    return new Response\EmptyResponse($status, $headers);
}

/**
 * Get PSR-7 Response object with Content-Type as json
 *
 * @param string $array
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function json($array, $status = 200, $headers = [])
{
    return new Response\JsonResponse($array, $status, $headers);
}

/**
 * Get PSR-7 Response object with redirect
 *
 * @param string $uri
 * @param int    $status
 * @param array  $headers
 * @return ResponseInterface
 */
function redirect($uri = '/', $status = 302, $headers = [])
{
    if (app('request')->isAjax()) {
        return ['redirect' => $uri];
    } else {
        return new Response\RedirectResponse($uri, $status, $headers);
    }
}

/**
 * Create a new redirect response to the previous location.
 *
 * @return Response
 */
function back()
{
    if (app('request')->isAjax()) {
        return ['reload' => 1];
    } else {
        $to = app('request')->getHeaderLine('Referer') ?: '/';
        return redirect($to);
    }
}

/**
 * Create a new response with downloadable file
 *
 * @param       $file
 * @param int   $status
 * @param array $headers
 * @return \App\Http\AttachmentResponse
 */
function download($file, $name = null, $status = 200, $headers = [])
{
    return new App\Http\AttachmentResponse($file, $name, $status, $headers);
}

/**
 * Create a new response with downloadable content
 *
 * @param       $content
 * @param int   $status
 * @param array $headers
 * @return Response
 */
function downloadContent($content, $name, $status = 200, $headers = [])
{
    $headers = array_replace($headers, [
        'content-length'      => strlen($content),
        'content-disposition' => sprintf('attachment; filename=%s', $name),
    ]);
    $response = new Response('php://temp', $status, $headers);
    $response->getBody()->write($content);
    if (!$response->hasHeader('Content-Type')) {
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
    }
    return $response;
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
 * @param string $extension
 * @return App\Config
 */
function config($extension)
{
    return new App\Config($extension, app('db'));
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

function restricted()
{
    if (!app('editor')->isAdmin()) {
        alert(t('error_mastercode_needed'), 'warning');
        $to = ifsetor(app('request')->headers['REFERER'], '/admin');
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
    $file = moduleDir($part[0]).'/models/'.$part[1].'.php';

    if (!file_exists($file)) {
        throw new \RuntimeException(sprintf(t('error_file_not_found'), $file));
    }

    include_once $file;
    $class = ucfirst($part[1]).'Model';

    return new $class();
}

/**
 * Loads view for some module
 * @param string $view module-name/view-name
 * @param array  $data
 * @return \App\View
 */
function view($view, $data = [])
{
    return new App\View($view, $data);
}

/**
 * @param string $file
 * @return mixed
 */
function parse_json_file($file)
{
    return json_decode(file_get_contents($file), true);
}

/**
 * @param string $file
 * @param array  $array
 * @return int
 */
function write_json_file($file, array $array)
{
    return file_put_contents($file, json_encode($array, JSON_PRETTY_PRINT));
}

/**
 * @param string $file
 * @param array  $array
 * @param bool   $process_sections
 * @return int
 */
function write_ini_file($file, array $array, $process_sections = false)
{
    $content = '';
    if ($process_sections) {
        foreach ($array as $key => $elem) {
            $content .= "[{$key}]\n";
            foreach ($elem as $key2 => $elem2) {
                if (is_array($elem2)) {
                    foreach ($elem2 as $key3 => $elem3) {
                        $content .= "{$key2}[{$key3}] = {$elem3}\n";
                    }
                } else {
                    $content .= "{$key2} = {$elem2}\n";
                }
            }
        }
    } else {
        foreach ($array as $key => $elem) {
            if (is_array($elem)) {
                foreach ($elem as $key2 => $elem2) {
                    $content .= "{$key}[{$key2}] = {$elem2}\n";
                }
            } else {
                $content .= "{$key} = {$elem}\n";
            }
        }
    }
    return file_put_contents($file, $content);
}

/**
 * Remove directory with all content
 *
 * @param string $dir Path to target folder
 */
function removeDir($dir)
{
    $d = opendir($dir);
    while (($entry = readdir($d)) !== false) {
        if ($entry != "." && $entry != "..") {
            is_dir($dir."/".$entry) ? removeDir($dir."/".$entry) : unlink($dir."/".$entry);
        }
    }
    closedir($d);
    rmdir($dir);
}

function csrf_field()
{
    return app('csrf')->getField();
}

/**
 * Generate a form field to spoof the HTTP verb used by forms.
 *
 * @param  string $method
 * @return string
 */
function method_field($method)
{
    return '<input type="hidden" name="_method" value="'.$method.'">';
}

function sortByWeight($a, $b)
{
    return $a['weight'] - $b['weight'];
}

function thumbnail($url, $width, $height, array $params = ['resize'])
{
    return $url;
}

function studly_case($value)
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
}

function camel_case($value)
{
    return lcfirst(studly_case($value));
}
