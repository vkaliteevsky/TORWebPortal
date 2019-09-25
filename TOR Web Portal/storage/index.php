<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
$seo['title'] = 'Склад';
/* Обработка запроса на обновление остатков на складе */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['jsonField'])) {
        $responseStatus = -13;
    } else if (!isset($_POST['userId'])) {
        $responseStatus = -6;
    }
    $curDt = date("Y-m-d H:i:s", time());
    if (isset($_POST['jsonField']) and (isset($_POST['userId']))) {
        $arr = (array)json_decode($_POST['jsonField']);
        $userId = $_POST['userId'];
        $userName = getUserName($userId);
//        print_r($arr);
//        $bodyText = "<b>Требуются запчасти:</b><br>";
        $bodyTextArr = array();
        $leftTextArr = array();
//        echo "<pre>".print_r($arr)."</pre>";
        for ($i = 0; $i < count($arr); $i++) {
            $item = (array)$arr[$i];
            $companyId = $item['companyId'];
            $spareId = $item['spareId'];
            $leftAmount = $item['leftAmount'];
            if (($leftAmount == "") or (empty($leftAmount)) or (strlen($leftAmount) <= 0)) {
                $leftAmount = 0;
            }
            $neededAmount = $item['neededAmount'];
//            echo $companyId . ":" . $spareId . ":" . $leftAmount . ":" . $neededAmount . "\n";
            $dbOutput = updateStorageInfo($companyId, $spareId, $userId, $curDt, $leftAmount);
            if (!$dbOutput) $responseStatus = -14;
            $spareInfo = getSpareInfo($spareId, $companyId);
            $spareCode = $spareInfo[0]['spare_code'];
            $spareName = $spareInfo[0]['spare_name'];
            if ((!empty($neededAmount)) and ($neededAmount > 0)) {
                $bodyTextArr[$companyId] .= formLine($spareName . " (" . $spareCode . ")", "требуется " . $neededAmount . " штук");
            }
            if (($leftAmount <= 2)) {
                $leftTextArr[$companyId] .= formLine($spareName . " (" . $spareCode . ")", "осталось " . $leftAmount . " штук");
            }
        }
        $companyNames = getCompanyNames();  // returns array companyId -> (company_short_name, company_long_name)
//        print_r($companyNames);
        foreach ($bodyTextArr as $cId => $text) {
            $companyName = $companyNames[$cId]['company_short_name'];
            $heading = "Запрос на запчасти от {$companyName}";
            $body = formLine("Инициатор", $userName);
            $body .= $bodyTextArr[$cId];
            $sendResponse = sendEmail(getStorageEmail($cId), "", $heading, $body);
        }
        foreach ($leftTextArr as $cId => $text) {
            $companyName = $companyNames[$cId]['company_short_name'];
            $heading = "Критический остаток на складе {$companyName}";
            $body = formLine("Инициатор", $userName);
            $body .= $leftTextArr[$cId];
            $sendResponse = sendEmail(getStorageEmail($cId), "", $heading, $body);
        }
        if (!isset($responseStatus)) {
            $responseStatus = 1;
        }
    }
}
/* Обработка завершена */

if (($roleId == 1) || ($roleId == 6)) {
    $storages = getStorageByCompanyId($userdata["company_id"]);
} else {
    $storages = getAllStoragies();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
    <style>
        .t_row:hover {
            cursor: default;
        }
    </style>
    <div class="workField">
        <div class="headingRow">
            <h1 class="heading">Склад</h1>
            <div class="searchBlock">
                <img src="../img/icons/search-disabled.png">
                <input type="text" id="search" placeholder="Тонер Xerox D110" oninput="filterByCriteria();">
            </div>
        </div>
        <form action="" method="POST" id="spareForm">
            <input type="hidden" name="jsonField" id="jsonField" value="">
            <input type="hidden" name="userId" id="userId" value="<? echo $userdata['user_id']; ?>">

            <div class="t_tb">
                <div class="t_heading">
                    <? if (($roleId >= 2) && ($roleId <= 5)) { ?>
                        <div class="t_cell companyCell">
                            <p>КОМПАНИЯ</p>
                        </div>
                    <? } ?>
                    <div class="t_cell objectCell">
                        <p>НАЗВАНИЕ</p>
                    </div>
                    <div class="t_cell serialCell">
                        <p>ИДЕНТИФИКАТОР</p>
                    </div>
                    <div class="t_cell leftAmountCell">
                        <p>ОСТАТОК</p>
                    </div>
                    <div class="t_cell neededAmountCell">
                        <p>ТРЕБУЕТСЯ</p>
                    </div>
                </div>
                <div class="t_body">
                    <? foreach ($storages as $k => $v) { ?>
                        <div class="t_row"
                             id="row<? echo $k; ?>"
                             data-spare="<? echo $v['spare_id']; ?>"
                             data-company="<? echo $v['company_id']; ?>"
                             data-search="<? echo $v['company_short_name'].";".$v['company_long_name'].";".$v['spare_code'].";".$v['spare_name']; ?>"
                        >
                            <? if (($roleId >= 2) && ($roleId <= 5)) { ?>
                                <div class="t_cell companyCell">
                                    <p><? echo $v['company_short_name']; ?></p>
                                </div>
                            <? } ?>
                            <div class="t_cell objectCell">
                                <p><? echo $storages[$k]["spare_name"]; ?></p>
                            </div>
                            <div class="t_cell serialCell">
                                <p><? echo $storages[$k]["spare_code"]; ?></p>
                            </div>
                            <div class="t_cell leftAmountCell">
                                <p>Остаток</p>
                                <input type="number" class="numberInput"
                                       id="leftAmount<? echo $k; ?>"
                                       style="max-width: 70px;"
                                       value="<? echo $storages[$k]["amount"]; ?>"
                                >
                            </div>
                            <div class="t_cell neededAmountCell">
                                <p>Требуется</p>
                                <input type="number" class="numberInput"
                                       id="neededAmount<? echo $k; ?>"
                                       style="max-width: 70px;"
                                >
                            </div>
                        </div>
                    <? } ?>
                </div>
            </div>
        </form>
        <div class="btn_list_div">
            <button id="btn_counter" class="btn btn_list" style="margin: 10px;" onclick="sendSpareInfo();">
                Отправить
            </button>
        </div>
    </div>
    <script>
        <? if(isset($responseStatus)) { ?>
        $(document).ready(function () {
            <? if ($responseStatus >= 1) { ?>
            showSuccessModal("Информация успешно обновлена", null);
            <? } else if ($responseStatus <= 0) { ?>
            <? $arrText = getErrorHeadingAndText($responseStatus); ?>
            showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
            <? } ?>
        });
        <? } ?>

        function filterByCriteria() {
            <? if ($roleId >= 1) { ?>
            var sub_name = document.getElementById("search").value.toUpperCase();
            var n = <? echo count($storages); ?>;
            for (var i = 0; i < n; i++) {
                //var company_name = document.getElementById("p" + i).innerHTML.toUpperCase();
                const fullText = $('#row'+i).attr('data-search').toUpperCase();
                const isContain = fullText.includes(sub_name);
                if (isContain) {
                    $("#row" + i).removeAttr("style");
                } else {
                    document.getElementById("row" + i).setAttribute("style", "display: none;");
                }
            }
            <? } ?>
        }

        function sendSpareInfo() {
            const isOk = checkSpareForm();
            if (!isOk) {
                return;
            }
            const arr = getArrayOfSpares();
            const jsonInfo = JSON.stringify(arr);
            $('#jsonField').val(jsonInfo);
            // alert(jsonInfo);
            $('#spareForm').submit();
        }

        function checkSpareForm() {
            const n = $('.t_body .t_row').length;
            // alert(n);
            let toSendForm = true;
            for (var i = 0; i < n; i++) {
                const leftObj = $("#leftAmount" + i);
                if (leftObj === undefined) {
                    continue;
                }
                const isOk1 = (hasOnlyDigits(leftObj.val()) || (leftObj.val() == ""));
                if (isOk1) {
                    leftObj.removeClass('errorSpare');
                } else {
                    leftObj.addClass('errorSpare');
                }

                const neededObj = $("#neededAmount" + i);
                const isOk2 = hasOnlyDigits(neededObj.val()) || (neededObj.val() == "");
                if (isOk2) {
                    neededObj.removeClass('errorSpare');
                } else {
                    neededObj.addClass('errorSpare');
                }
                // alert(isOk1 + " : " + isOk2);
                toSendForm = toSendForm && (isOk1 && isOk2);
            }

            return toSendForm;
        }

        function getArrayOfSpares() {
            var res = [];
            const n = $('.t_body .t_row').length;
            // alert("n = " + n);
            for (var i = 0; i < n; i++) {
                // alert(counterObj.attr('data-device-id'));
                // var deviceUnqId = $("#row"+i).attr('data-device');
                const leftAmountValue = $("#leftAmount" + i).val();
                let neededAmountValue;
                // if (isMobile()) {
                //     neededAmountValue = $("#neededAmountMob" + i).val();
                // } else {
                //     neededAmountValue = $("#neededAmount" + i).val();
                // }
                neededAmountValue = $("#neededAmount" + i).val();
                const companyId = $("#row" + i).attr('data-company');
                const spareId = $("#row" + i).attr('data-spare');
                // alert (neededAmountValue);
                if (neededAmountValue.length<=0) neededAmountValue = 0;

                const elem = {
                    i: i,
                    companyId: companyId,
                    spareId: spareId,
                    leftAmount: leftAmountValue,
                    neededAmount: neededAmountValue
                };
                res.push(elem);
            }
            return res;
        }
    </script>


<?php
require('../footer.php'); ?>