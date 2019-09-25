<?php
require_once ("../lib.php");

$orderId = $_POST['orderId'];
$name = $_POST['name'];
$filePath = "../../img/uploads/{$name}";

try {
    unlink($filePath);
} catch (Exception $e) {

}
$dbOutput = executeInsertRequest("delete from images_orders where (order_id = {$orderId}) and (image_src = '{$name}')");

