<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');

$userId = $_POST["userId"];
$companyId = $_POST["companyId"];
if (!isset($userId) or (!isset($companyId))) {
    echo "{}";
    return;
}
//echo $userId . " " . $userName . " " . $companyId;
//echo $userId . " " . $userName . " " . $deviceUnqId . " " . $counterValue;
//$userData = getUserInfo($userId);
$deviceInfo = getCompanyDevices($companyId, True);
echo $deviceInfo;
?>