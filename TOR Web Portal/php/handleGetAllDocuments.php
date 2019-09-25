<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/lib.php');

$orderId = $_GET['orderId'];
if (isset($orderId)) {
    $result = executeSelectRequest("select image_src from images_orders where order_id = {$orderId}");
    $response = "";
    $n = count($result);
    for ($i = 0; $i < $n-1; $i++) {
        $response .= $result[$i]['image_src'] . ";";
    }
    if ($n > 0) $response .= $result[$n-1]['image_src'];
    echo $response;
} else {
    echo "0";
}


