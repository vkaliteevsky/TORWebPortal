<?php
require_once("../lib.php");
$tmpPath = $_FILES['imgs']['tmp_name'];
$imgId = executeSelectRequest("select max(image_id) as mx from images_orders")[0]['mx'] + 1;
$initName = $_FILES['imgs']['name'];
$ext = pathinfo($initName, PATHINFO_EXTENSION);

$newFileName = "img_{$imgId}.{$ext}";
copy($_FILES['imgs']['tmp_name'], "../../img/uploads/{$newFileName}");
$dbOutput = insertImgSrc(8, $newFileName);