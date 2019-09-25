<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');
$userId = $_GET['user_id'];
if (!isset($userId)) {
    $userId = "";
}
setcookie("id", "", time() - 3600*24*30*12, "/");
setcookie("hash", "", time() - 3600*24*30*12, "/");
addLog($userId, 3);
header("Location: /");
exit;
?>