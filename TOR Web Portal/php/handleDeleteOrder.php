<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/php/auth_logic.php');

$userId = $_POST["userId"];
$orderId = $_POST['orderId'];
if (!isset($userId) || (!isset($orderId))) {
    echo "0";
    return;
}
$res = executeSelectRequest("select role_id from users where user_id = {$userId} limit 1");
$roleId = $res[0]['role_id'];
unset($res);

if (!canDeleteOrders($roleId, 1)) {
    echo "0";
    return;
}
//$dbOutput = deleteOrder($orderId);
$dbOutput = executeInsertRequest("update orders set order_status_id = 7 where order_id = {$orderId}");
if ($dbOutput) {
    echo "200";
} else {
    echo "0";
}

?>