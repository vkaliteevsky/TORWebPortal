<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
$seo['title'] = 'Оборудование';
$companies = getAllCompanies(False);
if ($roleId == 1) {
    $devices = getCompanyDevices($userdata["company_id"], False);
} else if ($roleId == 2) {
    header("Location: /orders/");
} else {
    $devices = getAllDevicesView(False);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
    <script>
        function filterByCompany() {
            <? if ($roleId != 1) { ?>
            var sub_name = document.getElementById("company_search").value.toUpperCase();
            var n = <? echo count($devices); ?>;
            for (var i = 0; i < n; i++) {
                var company_name = document.getElementById("p" + i).innerHTML.toUpperCase();
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
    <div id="device__page">
        <div class="row mb-4 whiteRow">
            <div class="col-xl-8 col-lg-6 col-md-6 col-12 text-center text-sm-left">
                <h1 class="heading" style="padding-left: 0px;">Оборудование</h1>
            </div>
            <?php if ($roleId != 1) { ?>
            <div class="col-xl-4 col-lg-6 col-md-6 col-12 text-right">
                <div class="searchBlock text-left">
                    <img src="../img/icons/search-disabled.png">
                    <input type="text" id="company_search" placeholder="Газпром" oninput="filterByCompany();">
                </div>
            </div>
            <?php } ?>
        </div>
        <!--
        <div class="row titleDev">
            <h1 class="dPageH1" style="">Оборудование</h1>
            <div class="col-12 text-left <? if ($roleId != 1) { ?> mb-4 <? } ?>">
                <? if ($roleId != 1) { ?>
                    <div class="searchCompanyDevice">
                        <img src="../img/icons/search-disabled.png" alt="">
                        <input type="text" id="company_search" placeholder="Газпром" oninput="filterByCompany();">
                    </div>
                <? } ?>
            </div>
        </div>
        -->
        <table>
            <thead>
            <tr class="sad">
                <? if ($roleId != 1) { ?>
                    <td style="width: 15%;" class="t1"><p class="p_tab" > КОМПАНИЯ <img src="../img/icons/chevron-down.png" alt=""></p></td>
                <? } ?>
                <td style="width: 20%;" class="t2"><p class="p_tab "  > МОДЕЛЬ <img src="../img/icons/chevron-down.png" alt=""></p></td>
                <td style="width: 20%;"class="t3"><p class="p_tab " > СЕРИЙНЫЙ НОМЕР <img src="../img/icons/chevron-down.png" alt=""></p></td>
                <td style="width: 25%;"class="t4"><p class="p_tab " > МЕСТОНАХОЖДЕНИЕ <img src="../img/icons/chevron-down.png" alt=""></p></td>
                <td style="width: 20%;"class="sad2"><p class="p_tab  d-none d-sm-block" > ОТВЕТСТВЕННЫЙ ИНЖЕНЕР <img src="../img/icons/chevron-down.png" alt=""></p></td>
            </tr>
            </thead>
            <tbody class="deviceList" id="deviceList" colspan="5">
            <? foreach ($devices as $k => $v) { ?>
                <tr id="row<? echo $k; ?>">
                    <? if ($roleId != 1) { ?>
                    <td class="sad">
                        <a><p id="p<? echo $k; ?>"><? echo $devices[$k]["company_short_name"]; ?><p></a>
                    </td>
                    <? } ?>
                    <td class="sad">
                        <a><p><? echo $devices[$k]["device_model"]; ?></p></a>
                    </td>
                    <td class="sad">
                        <a><p><? echo $devices[$k]["device_serial_number"]; ?></p></a>
                    </td>
                    <td class="devMobAll">
                        <a class="sad">
                            <p><? echo $devices[$k]["device_place"]; ?></p>
                        </a>
                        <div class="devMob">
                            <? if ($roleId != 1) { ?>
                                <p id="p<? echo $k; ?>"><span><? echo $devices[$k]["company_short_name"]; ?></span></p>
                            <? } ?>
                            <div class="devMobFLine">
                                <p><? echo $devices[$k]["device_model"];?></p>
                                <p> <? echo $devices[$k]["device_serial_number"]; ?></p>
                            </div>
                            <div class="devMobTLine">
                                <p> <? echo $devices[$k]["device_place"]; ?></p>
                                <p> <? echo $devices[$k]["user_short_name"]; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="sad2" >
                        <a><p><? echo $devices[$k]["user_short_name"]; ?></p></a>
                    </td>
                </tr>
                <? }; ?>
            </tbody>
        </table>
    </div>
    <style media="screen">

    </style>
<?php
require('../footer.php');
?>
