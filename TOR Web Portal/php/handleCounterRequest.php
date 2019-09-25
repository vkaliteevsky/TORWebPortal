<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');

//$counterValue = $_POST["counterValue"];
$userId = $_POST["userId"];
//$companyId = $_POST["companyId"];
//$deviceUnqId = $_POST["deviceUnqId"];
//$userName = $_POST["userName"];
$deviceCountersJSON = $_POST["deviceCountersJSON"];
$deviceCountersArr = json_decode($deviceCountersJSON, true);

$counterDt = date("Y-m-d H:i:s", time());
$text = "";
for ($i = 0; $i < count($deviceCountersArr); $i++) {
    $rs = updateCounterInfo($deviceCountersArr[$i]["companyId"], $deviceCountersArr[$i]["deviceUnqId"], $userId, $counterDt, $deviceCountersArr[$i]["counterValue"]);
}
echo "200";
//echo $text;
//echo $userId . ": " . $userName . ": " . $deviceUnqId . ": " . $counterValue . ": " . $companyId;
//$userData = getUserInfo($userId);
//$deviceInfo = getDeviceInfo($userId, $deviceUnqId);

/*
$text = "Получена информация по счетчику<br>";
$text .= "Пользователь: ".$userName."<br>";
$text .= "Устройство: ".$deviceInfo[0]["short_name"]."<br>";
$text .= "Значение счетчика: ".$counterValue."<br>";

try {
	sendEmail(getCounterEmail(), "", "Новый счетчик от " . $userName, $text);
	echo "200";
} catch (Exception $e) {
	handleEmailError($e);
	echo "-1";
}
*/

?>