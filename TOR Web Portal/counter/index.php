<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
if ($roleId == 6) {
    header("Location: ../storage/");
}
$seo['title'] = 'Подать счетчик';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /* Обработка отправленных ранее счетчиков */
    $toShowModal = False;
    if (!isset($_POST["userId"])) {
        $submitStatus = -6;
    }
    if (!isset($_POST["deviceCountersJSON"])) {
        $submitStatus = -7;
    }
    if (isset($_POST["userId"]) and (isset($_POST["deviceCountersJSON"]))) {
        $userId = $_POST["userId"];
        $deviceCountersJSON = $_POST["deviceCountersJSON"];
//        echo "TTT: " . $userId . ": " . $deviceCountersJSON;
        $deviceCountersArr = json_decode($deviceCountersJSON, true);
        $counterDt = date("Y-m-d H:i:s", time());

        $toShowModal = True;
        for ($i = 0; $i < count($deviceCountersArr); $i++) {
            try {
                $rs = updateCounterInfo($deviceCountersArr[$i]["companyId"], $deviceCountersArr[$i]["deviceUnqId"], $userId, $counterDt, $deviceCountersArr[$i]["counterValue"]);
            } catch (Exception $e) {
                $toShowModal = False;
                $submitStatus = 0;
            }
        }
    }
    if (!isset($submitStatus)) {
        $submitStatus = 1;
    }
}

if ($roleId == 1) {
    $devices = getCompanyDevicesWithCounters($userdata['company_id'], False);
} else {
    $devices = getAllCompanyDevicesWithCounters(False);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
    <div id="pjax-container">
        <div id="counter">
            <div class="mobh1">
                <h1>Подать счетчик</h1>
            </div>
            <!-- <pre><? print_r($devices); ?></pre> -->
            <? if ($roleId != 1) { ?>
                <div id="num__search_div" class="">
                    <img src="../img/icons/search-disabled.png" alt="">
                    <input type="text" сlass="num__search" class="mb-4" id="company_search" placeholder="Газпром"
                           oninput="filterByCompany();">
                </div>
            <? } ?>
            <form method="POST" action="" id="myForm">
                <input type="hidden" name="userId" id="userId" value="<? echo $userdata["user_id"]; ?>">
                <input type="hidden" name="deviceCountersJSON" id="deviceCountersJSON" value="">
                <div class="goods-out" id="goods-out">
                    <table>
                        <thead class="sad">
                        <? if ($roleId != 1) { ?>
                            <td><p class="p_tab" style="margin:auto"><br>КОМПАНИЯ</p></td>
                        <? } ?>
                        <td><p class="p_tab" style="margin:auto"><br>УСТРОЙСТВО</p></td>
                        <td><p class="p_tab" style="margin:auto"> ПРЕДЫДУЩИЕ<br>ПОКАЗАНИЯ</p></td>
                        <td><p class="p_tab lright" style="margin:auto"> НОВЫЕ<br>ПОКАЗАНИЯ</p></td>
                        </thead>
                        <? for ($i = 0; $i < count($devices); $i++) { ?>
                            <tr class="counterRow" id="row<? echo $i; ?>"
                                data-device="<? echo $devices[$i]['device_unq_id']; ?>"
                                data-company="<? echo $devices[$i]["company_id"]; ?>">
                                <? if ($roleId != 1) { ?>

                                    <td class="sad" id="company_name<? echo $i; ?>">
                                        <p>   <? echo $devices[$i]['company_short_name']; ?></p>
                                    </td>
                                <? } ?>
                                <td class="sad" id="device_model<? echo $i; ?>">
                                    <p> <? echo $devices[$i]['device_model']; ?>

                                        <span> <? echo $devices[$i]['device_serial_number']; ?></span></p>
                                </td>

                                <td class="sad" id="counter_value<? echo $i; ?>">
                                    <p>   <? echo $devices[$i]['counter_value']; ?></p>
                                </td>
                                <td >

                                    <div class="CounterMob" >
                                        <p class="NoSad">   <? echo $devices[$i]['device_model'];?> <span><? echo $devices[$i]['device_serial_number'];?> </span></p>
                                        <div style=" display: flex;">
                                            <div class="NoSad" style="width: 50vw;">
                                                <div class="NoSad_div" >
                                                    <h6 >ПРЕДЫДУЩИЕ<br> ПОКАЗАНИЯ</h6>
                                                    <h5><? echo $devices[$i]['counter_value']; ?></h5>
                                                </div>
                                            </div>
                                            <div class="NoSad_div">
                                                <h6 class="NoSad" style="display: block;"> НОВЫЕ <br>ПОКАЗАНИЯ</h6>
                                                <input class="" size="8"  type="number" value=""
                                                       id="counter<? echo $i; ?>">
                                            </div>
                                        </div>
                                    </div>


                                    <!--                                    <p id="pCounterError">Пожалуйста, проверьте <br> введенное значение</p>-->
                                </td>
                            </tr>
                        <? }; ?>
                    </table>
                </div>
            </form>
            <style>
                .NoSad{
                    display: none !important;
                }
                @media screen  and (max-width: 768px){
                    .NoSad_div{
                        width: 50%;
                        display: flex !important;
                    }
                    .NoSad{
                        display: table !important;
                    }
                }
            </style>
            <div class="btn_list_div">
                <button id="btn_counter" class="btn btn_list" style="margin: 10px;" onclick="sendCounterInfo(<? echo $roleId; ?>);">
                    Отправить
                </button>
            </div>
        </div>
    </div>
    <script>
        <? if (isset($submitStatus)) { ?>
        $(document).ready(function () {
            <? if ($submitStatus >= 1) { ?>
            showSuccessModal("Счетчики успешно обновлены", null);
            <? } else if ($submitStatus <= 0) { ?>
            <? $arrText = getErrorHeadingAndText($submitStatus); ?>
            showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
            <? } ?>
        });
        <? } ?>
        function filterByCompany() {
            <? if ($roleId != 1) { ?>
            var sub_name = document.getElementById("company_search").value.toUpperCase();
            var n = <? echo count($devices); ?>;
            for (var i = 0; i < n; i++) {
                var company_name = document.getElementById("company_name" + i).innerHTML.toUpperCase();
                var isContain = company_name.includes(sub_name);
                if (isContain) {
                    $("#row" + i).removeAttr("style");
                } else {
                    document.getElementById("row" + i).setAttribute("style", "display: none;");
                }
            }
            <? } ?>
        }
    </script>
    <style>

    </style>
<?php
require('../footer.php');
?>