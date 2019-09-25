<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["userId"])) {
        $submitStatus = -8;
    }
    if (!isset($_POST["orderId"])) {
        $submitStatus = -6;
    }
    if (isset($_POST["userId"]) and (isset($_POST["orderId"]))) {
//        $userId = $_POST["userId"];
        $userId = $userdata['user_id'];
        $orderId = $_POST["orderId"];
        $isVisible = isOrderVisibleForUser($orderId, $userId);
        if (!$isVisible) {
            $statusId = -25;
        }
        else {
            $deviceUnqId = $_POST["device"];
            $priorityId = $_POST["priority"];
            $statusId = $_POST["status"];
            $clientText = $_POST["clientText"];
            $engineerResponse = $_POST["engineerResponse"];
            $toContinueWork = $_POST["toContinueWork"];
            $engineerId = $_POST["engineer"];
            $managerText = $_POST["managerText"];
            $arrivalDt = $_POST["arrival_dt"];
//    echo "YYY:" . $arrivalDt."UUU";
            $counterValue = $_POST["counter"];
            $companyId = getCompanyIdByOrderId($orderId);

            /* случай, если прилетела команда об отмене заявки */
            if (isset($_POST['cancelFlag'])) {
                if ($_POST['cancelFlag'] == 1) {
                    $statusId = 6;
                }
            }

            if (isset($_POST['sparesInput'])) {
                $sparesInput = $_POST['sparesInput'];
                $sparesArr = json_decode($sparesInput);
                //$roleId = getRoleIdByUserId($userId);
                $dbOutput = executeInsertRequest("delete from orders_spares where (order_id = {$orderId})");
                if (!$dbOutput) {
                    $submitStatus = -24;
                }
                //print_r($sparesArr);
                for ($i = 0; $i < count($sparesArr); $i++) {
                    $item = (array)$sparesArr[$i];
                    $spareId = $item['spareId'];
                    $spareAmount = $item['amount'];
                    if ((strlen($spareId) > 0) and (strlen($spareAmount) > 0)) {
                        $dbOutput = insertOrdersSpares($orderId, $spareId, $userId, $spareAmount);
                        if (!$dbOutput) {
                            $submitStatus = -23;
                        }
                    }
                }
            }

            $currentOrder = getOrderInfo($orderId, False);	// состояние заявки до внесения изменений
            $detailedOrder = getDetailedInfoAboutOrder($orderId, False);
            $dt = date("Y-m-d H:i:s", time());  // текущее время обновления заявки

            if (isset($counterValue) and (!empty($counterValue))) {
//        echo $companyId.": ". $deviceUnqId.": " .$userId.": ".$dt.": ".$counterValue;
                updateCounterInfo($companyId, $deviceUnqId, $userId, $dt, $counterValue);
            }

            //$dbOutput = insertOrderIntoHistoryTable($orderId);
            //if (isDBResponseOk($dbOutput)) {
            //    $submitStatus = -9;
            //}

            $clientText = prepareString($clientText);
            $engineerResponse = prepareString($engineerResponse);
            $managerText = prepareString($managerText);

            $statusIdText = formUpdateParameter("order_status_id", $statusId, False);
            $priorityIdText = formUpdateParameter("order_priority_id", $priorityId, False);
            $managerTextReq = formUpdateParameter("order_inner_text", $managerText, True);
            $engineerResponseReq = formUpdateParameter("order_text_response", $engineerResponse, True);
            $clientTextReq = formUpdateParameter("order_text_request", $clientText, True);
            $arrivalDtReq = formUpdateParameter("engineer_arrival_dt", $arrivalDt, True);
            $toContinueWorkText = formUpdateParameter("order_to_continue_work", $toContinueWork, False);
            $deviceUnqIdText = formUpdateParameter("device_unq_id", $deviceUnqId, False);
            $updateEngineerIdText = formUpdateParameter("user_id_engineer", $engineerId, False);
            if ($engineerId == -1) { $updateEngineerIdText = "user_id_engineer = \"\", "; }
            $toContinueWorkText = "";

            $sql = "update orders set user_id = {$userId}, {$deviceUnqIdText}{$priorityIdText}{$statusIdText}";
            $sql .= "{$clientTextReq}{$engineerResponseReq}{$toContinueWorkText}";
            $sql .= "{$updateEngineerIdText}{$arrivalDtReq}{$managerTextReq} order_dt_creation = '{$dt}' where order_id = {$orderId}";

            $dbOutput = executeInsertRequest($sql);
            if (!isDBResponseOk($dbOutput)) {
                $submitStatus = 0;
            }

            /* Обработка видимости Visibility
            if (!empty($updateEngineerIdText)) {
                // изменение области видимости заявки orderId для инженеров
                if (!empty($currentOrder["user_id_engineer"])) {
                    $dbOutput = makeOrderUnVisibleForUser($orderId, $currentOrder["user_id_engineer"]);
                    if (!isDBResponseOk($dbOutput)) $submitStatus = -11;

                }
                $dbOutput = makeOrderVisibleForUser($orderId, $engineerId);
                if (!isDBResponseOk($dbOutput)) $submitStatus = -10;

            }
            --- */

            informUsers($orderId, $detailedOrder);
            if (!isset($submitStatus)) {
                $submitStatus = 1;
            }
        }
    }
}


//header("Location: /order/index.php?order_id={$orderId}");
//return;
?>