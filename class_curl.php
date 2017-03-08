<?php
include("use.php");
function curl($url = '', $var = '', $header = false, $nobody = false)
{
    global $config, $sock;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, $header);
    curl_setopt($curl, CURLOPT_HEADER, $nobody);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, random_uagent());
    curl_setopt($curl, CURLOPT_REFERER, 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_run-check-cookie-submit&redirectCmd=_login-submit');
    if ($var) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $var);
    }
    curl_setopt($curl, CURLOPT_COOKIEFILE, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $config['cookie_file']);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
function fetch_value($str, $find_start, $find_end)
{
    $start = strpos($str, $find_start);
    if ($start === false) {
        return "";
    }
    $length = strlen($find_start);
    $end    = strpos(substr($str, $start + $length), $find_end);
    return trim(substr($str, $start + $length, $end));
}

function get($list)
{
	preg_match_all("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}/", $list, $socks);
	return $socks[0];
}

function xflush()
{
    static $output_handler = null;
    if ($output_handler === null)
    {
        $output_handler = @ini_get('output_handler');
    }
    if ($output_handler == 'ob_gzhandler')
    {
        return;
    }
    flush();
    if (function_exists('ob_flush') and function_exists('ob_get_length') and
        ob_get_length() !== false)
    {
        @ob_flush();
    } else
        if (function_exists('ob_end_flush') and function_exists('ob_start') and
            function_exists('ob_get_length') and ob_get_length() !== false)
        {
            @ob_end_flush();
            @ob_start();
        }
}
function getCookies($str){
	preg_match_all('/Set-Cookie: ([^; ]+)(;| )/si', $str, $matches);
	$cookies = implode(";", $matches[1]);
	return $cookies;
}

function array_remove_empty($arr)
{
    $narr = array();
    while (list($key, $val) = each($arr))
    {
        if (is_array($val))
        {
            $val = array_remove_empty($val);
            // does the result array contain anything?
            if (count($val) != 0)
            {
                // yes :-)
                $narr[$key] = trim($val);
            }
        } else
        {
            if (trim($val) != "")
            {
                $narr[$key] = trim($val);
            }
        }
    }
    unset($arr);
    return $narr;
}

function display($m, $t = 1, $d = 0)
{
    if ($t == 1)
    {
        echo '<div>' . $m . '</div>';
    } else
    {
        echo $m;
    }
    if ($d)
    {
        exit;
    }
}
function delete_cookies()
{
    global $config;
    $fp = @fopen($config['cookie_file'], 'w');
    @fclose($fp);
}
