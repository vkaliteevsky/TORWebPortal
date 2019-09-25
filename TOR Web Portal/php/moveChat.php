<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');

$sql = "select order_id, user_id, order_dt_creation, user_id_engineer, order_text_request, order_text_response, order_inner_text from all_orders
    where ((order_id <= 57) and (order_id <> 8)) order by order_id asc, order_dt_creation asc;
";
$lines = executeSelectRequest($sql);

//echo "<pre>".print_r($lines)."</pre>";
function addDtSeconds($dt, $x) {
    $d = DateTime::createFromFormat("Y-m-d H:i:s", $dt);
    $d->add(new DateInterval("PT{$x}S"));
    return $d->format('Y-m-d H:i:s');
}

$lastOrderId = -1;
$lastRequestText = "";
$lastResponseText = "";
$lastManagerText = "";
foreach ($lines as $arr) {
    $orderId = $arr['order_id'];
    $userId = $arr['user_id'];
    $dt = $arr['order_dt_creation'];
    $requestText = $arr['order_text_request'];
    $responseText = $arr['order_text_response'];
    $managerText = $arr['order_inner_text'];
//    echo $dt . "\n";
//    echo addDtSeconds($dt, 1) . "\n";
//    echo addDtSeconds($dt, 2) . "\n";
//    return;
    if (!empty($dt)) {
        if ($orderId <> $lastOrderId) {
            if (strlen($requestText) > 1) {
                addMessage($orderId, $userId, $dt, $requestText);
            } else {
                echo "Error Type 1: OrderId = {$orderId}\n";
            }
            $lastRequestText = $requestText;
            $lastResponseText = $responseText;
            $lastManagerText = $managerText;
        } else {
            if ((strlen($requestText) > 3) and (strcmp($requestText, $lastRequestText))) {
                addMessage($orderId, $userId, $dt, $requestText);
            }
            if ((strlen($responseText) > 3) and (strcmp($responseText, $lastResponseText))) {
                addMessage($orderId, $userId, addDtSeconds($dt, 1), $responseText);
            }
            if ((strlen($managerText) > 3) and (strcmp($managerText, $lastManagerText))) {
                if ($orderId == 27) {
                    echo "Manager Text: {$managerText}<br>Old Manager Text: {$lastManagerText}<br><br>";
                }
                addMessage($orderId, $userId, addDtSeconds($dt, 2), $managerText);
            }
            $lastRequestText = (strlen($requestText) > 3) ? $requestText : $lastRequestText;
            $lastResponseText = (strlen($responseText) > 3) ? $responseText : $lastResponseText;
            $lastManagerText = (strlen($managerText) > 3) ? $managerText : $lastManagerText;
        }
    }
    $lastOrderId = $orderId;
}