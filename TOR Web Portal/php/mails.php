<?

function sendEmail($to, $copy, $heading, $text) {
    $debugMode = false;
	$headers = "From: Tor Service<info@lpcc.ru>\r\n";
	$headers .= "Reply-To: service@tor-service.ru\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	if ($debugMode) {
        $headers .= "Bcc: info@dig-studio.ru\r\n";
    } else {
        $headers .= "Bcc: info@dig-studio.ru,tech@tor-service.ru\r\n";
    }
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	$message = $text;
    $rs = true;
    $host = $_SERVER['HTTP_HOST'];
    if (strcmp($host, "lk.lpcc.ru") != 0) {
        $to = "info@dig-studio.ru";
    }
    if ($debugMode) {
        $to = "info@dig-studio.ru";
    }
    $rs = mail($to, $heading, $message, $headers);
	return $rs;
}

function sendErrorByEmail($text) {
	sendEmail("gik06@yandex.ru", "", "Error on TOR Site", $text);
}
function sendOrderInfoByEmail($companyId, $orderId) {
	//echo "select order_id, device_unq_id, status_id, priority_id, text_request from orders where (user_id=".$userId." and order_id=".$orderId.") order by dt desc limit 1";
	$result = executeSelectRequest("select order_id, device_unq_id, order_status_id, order_priority_id, order_text_request from orders where (company_id=".$companyId." and order_id=".$orderId.") order by order_dt_creation desc limit 1");
	$text = "";
	foreach ($result as $key => $value) {
		$text .= $key . ": " . $value . "\n";
	}
	sendEmail("gik06@yandex.ru", "", "Информация по заявке ".$orderId." от пользователя " . $userId, $text);
}

function informUsers($orderId, $lastOrder) {
    $currentOrder = getDetailedInfoAboutOrder($orderId, False);
//    print_r($currentOrder);
    $curStatusId = $currentOrder['status_id'];
//    echo "TTT: {$curStatusId}";
//    print_r ($lastOrder);
    if (!empty($lastOrder)) {
        $lastStatusId = $lastOrder['status_id'];
    } else {
        $lastStatusId = -1;
    }
//    echo "YYY: " . $lastStatusId . " " . $curStatusId;
    if (($lastStatusId == -1) and ($curStatusId == 1)) {  // создание новой заявки
        $heading = "Оформлена новая заявка от {$currentOrder['company_short_name']}";
        $body = makeCreatedEmailBody($currentOrder);
        sendEmail(getProjectManagerEmail($currentOrder['company_id']), "", $heading, $body);
        sendEmail(getServiceManagerEmail(), "", $heading, $body);
//        echo $heading . ": " . $body . ": " . getProjectManagerEmail($currentOrder['company_id']);
    } else if (($lastStatusId == 1) and ($curStatusId == 2)) {  // переход в статус "Назначена на инженера"
        $engineerName = $currentOrder['engineer_short_name'];
//        $heading = "Заявка {$orderId} назначена на инженера {$engineerName}";
        $heading = "Назначение заявки №{$orderId} от {$currentOrder['company_short_name']}";
        $body = makeAppointedEmailBody($currentOrder);
        $engineerEmail = getEngineerEmail($orderId);
//        echo $engineerEmail . ": " . $heading . ": " . $body;
        sendEmail($engineerEmail, "", $heading, $body);
    } else if (($lastStatusId == 2) and ($curStatusId == 3)) {   // переход в статус "В работе"
        $heading = "Заявка №{$orderId} принята в работу";
        $body = makeInWorkEmailBody($currentOrder);
        $clientEmail = getCompanyEmail($currentOrder['company_id']);
        $arrivalTime = $currentOrder['engineer_arrival_dt'];
        $curTime = date("Y-m-d H:i:s", time());  // текущее время обновления заявки
//        if ($curTime <= $arrivalTime) {
//            sendEmail($clientEmail, "", $heading, $body);
//        }
        sendEmail($clientEmail, "", $heading, $body);
//        echo $clientEmail . ": " . $heading . ": " . $body. ": " . $arrivalTime . ": " . $curTime;
    } else if (($curStatusId == 4) or ($curStatusId == 5)) {
        $heading = "Заявка #{$orderId} выполнена";
        $body = makeExecutedEmailBody($currentOrder, True);
        $clientEmail = getCompanyEmail($currentOrder['company_id']);
        sendEmail($clientEmail, "", $heading, $body);  // отправка уведомления заказчику
//        echo $clientEmail . ": " . $heading . ": " . $body;

        $heading = "Заявка {$orderId} от {$currentOrder['company_short_name']} выполнена";
        $body = makeExecutedEmailBody($currentOrder, False);
        sendEmail(getProjectManagerEmail($currentOrder['company_id']), "", $heading, $body);
        sendEmail(getServiceManagerEmail(), "", $heading, $body);
    }
    //sendEmail("gik06@yandex.ru", "", "Тест", createHTMLInfomBody($currentOrder));
}
function makeExecutedEmailBody($currentOrder, $isForClient) {
    if ($isForClient) {
        $text = formLine("ID заявки", $currentOrder['order_id']);
        $text .= formLine("Устройство", $currentOrder['device_model'] . " (" . $currentOrder['device_serial_number'] . ")");
        $text .= formLine("Статус", $currentOrder['status_name']);
        $text .= formLine("Комментарий инженера", $currentOrder['order_text_response']);
    } else {
        $text = formLine("ID заявки", $currentOrder['order_id']);
        $text .= formLine("Компания", $currentOrder['company_short_name']);
        $text .= formLine("Устройство", $currentOrder['device_model'] . " (" . $currentOrder['device_serial_number'] . ")");
        $text .= formLine("Статус", $currentOrder['status_name']);
        $text .= formLine("Приоритет", $currentOrder['priority_name']);
        $text .= formLine("Описание", $currentOrder['order_text_request']);
        $text .= formLine("Инженер", $currentOrder['engineer_short_name']);
    }
    return $text;
}
function makeInWorkEmailBody($currentOrder) {
    $text = formLine("Имя инженера", $currentOrder['engineer_short_name']);
    $arrivalDt = $currentOrder['engineer_arrival_dt'];
    $arrivalDt = ($arrivalDt == 0) ? "-" : $arrivalDt;
    $text .= formLine("Дата приезда инженера", $arrivalDt);
    return $text;
}
function makeCreatedEmailBody($orderInfo) {
    $text = formLine("ID заявки", $orderInfo['order_id']);
    $text .= formLine("Компания", $orderInfo['company_short_name']);
    $text .= formLine("Устройство", $orderInfo['device_model'] . " (" . $orderInfo['device_serial_number'] . ")");
    $text .= formLine("Статус", $orderInfo['status_name']);
    $text .= formLine("Приоритет", $orderInfo['priority_name']);
    $text .= formLine("Описание", $orderInfo['order_text_request']);
    return $text;
}
function makeCreatedEmailBodyForClient($orderInfo) {
    $text = formLine("ID заявки", $orderInfo['order_id']);
    $text .= formLine("Устройство", $orderInfo['device_model'] . " (" . $orderInfo['device_serial_number'] . ")");
    $text .= formLine("Статус", $orderInfo['status_name']);
    $text .= formLine("Приоритет", $orderInfo['priority_name']);
    $text .= formLine("Описание", $orderInfo['order_text_request']);
    return $text;
}
function makeAppointedEmailBody($orderInfo) {
    $text = formLine("ID заявки", $orderInfo['order_id']);
    $text .= formLine("Компания", $orderInfo['company_short_name']);
    $text .= formLine("Статус", $orderInfo['status_name']);
    $text .= formLine("Приоритет", $orderInfo['priority_name']);
    $text .= formLine("Описание", $orderInfo['order_text_request']);
    $text .= formLine("Инженер", $orderInfo['engineer_short_name']);
    return $text;
}
function createHTMLInfomBody($orderInfo) {
    $text = "Текущее состояние заявки:<br />";
    $text .= formLine("Статус", $orderInfo['status_name']);
    $text .= formLine("Приоритет", $orderInfo['priority_name']);
    return $text;
}
function formLine($name, $value) {
    if (empty($value)) {
        $value = "-";
    }
    return "<b>{$name}:</b> {$value}<br />";
}
function getSupportEmail() {
	return "service@tor-service.ru";
}
function getCounterEmail() {
	return "service@tor-service.ru";
}
function getNewOrderEmail() {
	return "service@tor-service.ru";
}
function getProjectManagerEmail($companyId) {
    // return "panteleeva@tor-service.ru";
    return "kolbasyuk@tor-service.ru,panteleeva@tor-service.ru,moiseenko@tor-service.ru";
}
function getServiceManagerEmail() {
    return "service@tor-service.ru";
}
function getEngineerEmail($orderId) {
    $result = executeSelectRequest("select users.user_email from orders left join users on (orders.user_id_engineer = users.user_id) where (orders.order_id = {$orderId})");
    return $result[0]['user_email'];
}
function getUserEmail($userId) {
    $result = executeSelectRequest("select users.user_email from users where (user_id = {$userId})");
    return $result[0]['user_email'];
}
function getStorageEmail($companyId) {
//    return "service@tor-service.ru";
    return "kolbasyuk@tor-service.ru,panteleeva@tor-service.ru,moiseenko@tor-service.ru";
//    return "gik06@yandex.ru,info@dig-studio.ru";
}
?>