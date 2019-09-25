<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');

$orderId = $_POST['orderId'];
$userId = $userdata['user_id'];

if (!isset($orderId) || (empty($orderId))) {
    echo "[]";
    return;
}
$isOrderVisible = isOrderVisibleForUser($orderId, $userId);
if (!$isOrderVisible) {
    echo "[]";
    return;
}

$rs = getAllMessages($orderId, true);
echo $rs;
//echo "orderId: {$orderId}, userId: {$userId}, dt: {$dt}, text: {$text}";

?>