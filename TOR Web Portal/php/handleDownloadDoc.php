<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/lib.php');

$orderId = $_GET['orderId'];
$docId = $_GET['docId'];
if (isset($docId) and isset($orderId)) {
    $result = executeSelectRequest("select image_src from images_orders where (order_id = {$orderId} and image_id = {$docId})");
    $n = count($result);
    if ($n == 1) {
        $path = "../img/uploads/" . $result[0]['image_src'];
    }
    if (file_exists($path)) {
        downloadFile($path);
    } else {
        downloadFile("error");
    }
}


