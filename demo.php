<?php
error_reporting(E_ERROR | E_PARSE);
set_time_limit (10);
class SMTP_validateEmail {


var $sock;
var $user;
var $domain;
var $domains;
var $port = 25;
var $max_conn_time = 10;
var $max_read_time = 5;

var $from_user = 'user';

var $from_domain = 'localhost';


var $nameservers = array(
'192.168.0.1'
);

var $debug = true;

function SMTP_validateEmail($emails = false, $sender = false) {
if ($emails) {
$this->setEmails($emails);
}
if ($sender) {
$this->setSenderEmail($sender);
}
}

function _parseEmail($email) {
$parts = explode('@', $email);
$domain = array_pop($parts);
$user= implode('@', $parts);
return array($user, $domain);
}


function setEmails($emails) {
foreach($emails as $email) {
list($user, $domain) = $this->_parseEmail($email);
if (!isset($this->domains[$domain])) {
$this->domains[$domain] = array();
}
$this->domains[$domain][] = $user;
}
}


function setSenderEmail($email) {
$parts = $this->_parseEmail($email);
$this->from_user = $parts[0];
$this->from_domain = $parts[1];
}


function validate($emails = false, $sender = false) {
$results = array();
if (function_exists('stream_context_create') && function_exists('stream_socket_client')) {
    $socket_options = array('socket' => array('bindto' => '0:0'));
//stream_context_set_option($socket_context, 'socket', 'proxy', 'tcp://218.108.114.140:8080');
//stream_context_set_option($socket_context, 'socket', 'request_fulluri', true);
  $socket_context = stream_context_create($socket_options);
}

if ($emails) {
$this->setEmails($emails);
}
if ($sender) {
$this->setSenderEmail($sender);
}
foreach($this->domains as $domain=>$users) {

$mxs = array();
$this->domain = $domain;
list($hosts, $mxweights) = $this->queryMX($domain);
for($n=0; $n < count($hosts); $n++){
$mxs[$hosts[$n]] = $mxweights[$n];
}
asort($mxs);
$mxs[$this->domain] = 0;

$this->debug(print_r($mxs, 1));

$timeout = $this->max_conn_time;
while(list($host) = each($mxs)) {
$this->debug("try $host:$this->port\n");

$socme= stream_socket_client($host.':'.$this->port, $errno, $errstr, (float) $timeout, STREAM_CLIENT_CONNECT| STREAM_CLIENT_PERSISTENT, $socket_context);
if($errstr){
foreach($users as $user) {
$results[$user.'@'.$domain] = '4';
}
}
if ($this->sock = $socme ) {
stream_set_timeout($this->sock, $this->max_read_time);
break;
}
}
if ($this->sock) {
$reply = fread($this->sock, 2082);
$this->debug("<<<\n$reply");

preg_match('/^([0-9]{3}) /ims', $reply, $matches);
$code = isset($matches[1]) ? $matches[1] : '';

if($code != '220') {
foreach($users as $user) {
$results[$user.'@'.$domain] = '2';
}
continue;
}
$this->send("HELO ".$this->from_domain);
$resTo = $this->send("MAIL FROM: <".$this->from_user.'@'.$this->from_domain.">");
preg_match('/^([0-9]{3}) /ims', $resTo, $matches2);
$code2 = isset($matches2[1]) ? $matches2[1] : '';
if($code2 == '550') {
foreach($users as $user) {
$results[$user.'@'.$domain] = '3';
}
continue;
}
foreach($users as $user) {
$reply = $this->send("RCPT TO: <".$user.'@'.$domain.">");
preg_match('/^([0-9]{3}) /ims', $reply, $matches);
$code = isset($matches[1]) ? $matches[1] : '';

if ($code == '250') {
$results[$user.'@'.$domain] = '1';
} elseif ($code == '451' || $code == '452') {
$results[$user.'@'.$domain] = '1';
} else {
$results[$user.'@'.$domain] = '2';
}

}

$this->send("RSET");

// quit
$this->send("quit");
fclose($this->sock);

}
}
return $results;
}



function send($msg) {
fwrite($this->sock, $msg."\r\n");

$reply = fread($this->sock, 2082);

$this->debug(">>>\n$msg\n");
$this->debug("<<<\n$reply");

return $reply;
}


function queryMX($domain) {
$hosts = array();
$mxweights = array();
if (function_exists('getmxrr')) {
getmxrr($domain, $hosts, $mxweights);
} else {
// windows, we need Net_DNS
require_once 'Net/DNS.php';

$resolver = new Net_DNS_Resolver();
$resolver->debug = $this->debug;
// nameservers to query
$resolver->nameservers = $this->nameservers;
$resp = $resolver->query($domain, 'MX');
if ($resp) {
foreach($resp->answer as $answer) {
$hosts[] = $answer->exchange;
$mxweights[] = $answer->preference;
}
}

}
return array($hosts, $mxweights);
}


function microtime_float() {
list($usec, $sec) = explode(" ", microtime());
return ((float)$usec + (float)$sec);
}

function debug($str) {
if ($this->debug) {
echo '<pre>'.htmlentities($str).'</pre>';
}
}

}
function generateCode($alphaCode,$varSa=0){
	$ranges = "";
	if($varSa == 1){
	$range1 = range("a","z");
	$range2 = range("a","z");
	$range3 = range("a","z");
	}else{
		$range1 = range("0","9");
		$range2 = range("0","9");
		$range3 = range("0","9");
	}
	$ranges .= implode('',$range1);
	$ranges .= implode('',$range2);
	$ranges .= implode('',$range3);
	//array_push($ranges,$range3);
	$rangesText = '';
      $i = 0;
    while ($i < $alphaCode) { 
		$rangesText .= substr($ranges, mt_rand(0, strlen($ranges)-1), 1);
		$i++;
	}
	return $rangesText;
}
function writeDoc($confFile,$reString){
	if (!is_writable($confFile)) {
		@chmod($confFile, 0777); 
	}
		if (!$handle = fopen($confFile, 'a+')) {
			 return $confFile." Açýlamadý!";
		}
		if (fwrite($handle, $reString) === FALSE) {
			return $confFile." Yazma izni bulunamadý!";
		}
		@chmod($confFile, 0644); 
		return $confFile." Kaydedildi.";
		fclose($handle);
}

$SMTP_Validator = new SMTP_validateEmail();
$mailList = array();
$mailList[$_GET["email"]] = $_GET["email"];
$SMTP_Validator->debug = false;
$sender = 'webmaster@heroku.com';
$results = $SMTP_Validator->validate($mailList	, $sender);
$end = time();
$totaltime = ($end - $start)  ; 
echo $results[$_GET["email"]];
$hours = intval($totaltime / 3600);   
$seconds_remain = ($totaltime - ($hours * 3600)); 

$minutes = intval($seconds_remain / 60);   
$seconds = ($seconds_remain - ($minutes * 60)); 

?>
