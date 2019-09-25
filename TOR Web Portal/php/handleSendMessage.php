<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');

$orderId = $_POST['orderId'];
$userId = $userdata['user_id'];
$text = $_POST["messageText"];
$dt = date("Y-m-d H:i:s", time());
$text = prepareString($text);
//$orderId = 8; $userId = 9; $text = "Hello World";

if (!isset($orderId) || (empty($orderId))) {
    echo "[]";
    return;
}
$isOrderVisible = isOrderVisibleForUser($orderId, $userId);
if (!$isOrderVisible) {
    echo "[]";
    return;
}
if (strlen($text) <= 0) {
    echo "[]";
    return;
}

$rs = addMessage($orderId, $userId, $dt, $text);
if (!$rs) {
    echo "[]";
    return;
}
$result = getLastMessage($orderId, $userId, true);
echo $result;
//echo "orderId: {$orderId}, userId: {$userId}, dt: {$dt}, text: {$text}";

?>