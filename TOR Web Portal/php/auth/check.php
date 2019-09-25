<?
// Скрипт проверки
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');
function updatePassword($userId, $password) {
    $hashCode = md5(md5($password));
    $res = executeInsertRequest("update users set user_password = '{$hashCode}' where user_id = {$userId}");
    return $res;
}
//echo $_COOKIE['id'] . " " . $_COOKIE['hash'];

$GLOBALS['conn'] = getConnection();
$conn=$GLOBALS['conn'];

$roleId = -1;
$userdata = "";

if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
	//echo $_COOKIE['id'] . " " . $_COOKIE['hash'];
    $query = $conn->query("SELECT * FROM user_info WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);
	//print_r ($userdata);
    if(($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id']))
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/");
        //echo "Wrong hash";
        addLog($userdata['user_id'], 10);
		header("Location: /");
    }
    else
    {
        setcookie("id", $_COOKIE['id'], time() + 30*60, "/");
        setcookie("hash", $_COOKIE['hash'], time() + 30*60, "/");
		$roleId = $userdata['role_id'];
		addLog($userdata['user_id'], 11);
        //echo "Привет, ".$userdata['login'].". Всё работает!";
    }
}
else
{
    //echo "Wrong hash 2";
    addLog("", 9);
	header("Location: /");
}
?>