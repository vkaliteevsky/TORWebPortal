<?php
require_once("../lib.php");

$orderId = $_POST['orderId'];
$imgs = executeSelectRequest("select * from images_orders where (order_id = {$orderId})");
$arr = array();
for ($i = 0; $i < count($imgs); $i++) {
    $img = $imgs[$i];
    $fileName = $img['image_src'];
    $fileSize = 100;
    $filePath = "/img/uploads/{$fileName}";
    $arr[] = array('name'=>$fileName, 'size'=>$fileSize, 'path'=>$filePath);
}

echo json_encode($arr);