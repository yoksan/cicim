<?php
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 1);
ini_set('memory_limit','512M');
ini_set('max_execution_time','999');
ini_set('max_input_time','999');
set_time_limit (999);
error_reporting(E_ALL);
date_default_timezone_set("Europe/Istanbul");
header("Content-type: text/html; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
$to      = 'info@cevapsende.com';
$subject = 'the subject';
$message = 'hello';
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

var_dump(mail($to, $subject, $message, $headers));
// print_r($sonuc);

phpinfo();
?>
