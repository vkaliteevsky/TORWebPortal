<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/lib.php');

$imgNamesStr = $_GET['imgNames'];
//$imgNamesStr = "img_1.JPG;img_22.jpg;img_25.jpeg";
if (isset($imgNamesStr)) {
    $imgNames = explode(";", $imgNamesStr);
    $paths = array();
    for ($i = 0; $i < count($imgNames); $i++) {
        $paths[] = "../img/uploads/" . $imgNames[$i];
    }
    $zipPath = zipFiles($paths);
    downloadFile($zipPath);
    unlink($zipPath);
}


