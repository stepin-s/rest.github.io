<?php
/**
 * This example shows sending a message using a local sendmail binary.
 */

require 'Mail.php';

$mail = new Mail();
$mail->setPost();
$mail->setLanguage( isset($_COOKIE['lng']) ? $_COOKIE['lng'] : 'ru');

$mail->send();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($mail->getAnswer());



