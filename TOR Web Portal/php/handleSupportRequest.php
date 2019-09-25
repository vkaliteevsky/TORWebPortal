<?php
require_once('lib.php');

$text = $_POST["text_request"];
$userId = $_POST["user_id"];
if (!isset($userId) or (!isset($text))) {
    echo "-1";
    return;
}

$userData = getUserInfo($userId);
try {
    sendEmail(getSupportEmail(), "", "Новый запрос от " . $userData["short_name"], $text);
    echo "200";
} catch (Exception $e) {
    handleEmailError($e);
    echo "-1";
}


?>