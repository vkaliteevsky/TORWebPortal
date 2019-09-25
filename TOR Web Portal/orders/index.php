<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
if ($roleId == 6) {
    header("Location: ../storage/");
}
$seo['title'] = 'Список заявок';
require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
$orders = getVisibleOrders($userdata["user_id"], False, $roleId); // contains list of visible orders
foreach ($orders as $order) {
    $oid = $order['order_id'];
    $creationDts[$oid] = getCreatedDateTime($oid);
}
//sendEmail("info@dig-studio.ru", "", "Заголовок письма", "<h1>Тело письма</h1>");
?>
<style>
    @media (max-width: 400px) {
        #index__page #status_filter {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        #index__page .swOrders {
            margin: 15px 0px 0px 0px;
        }
    }
</style>
    <div id="index__page">
        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>
<!--        <pre>--><?// print_r($orders); ?><!--</pre>-->
        <div class="row mob_center">
            <div class="col-lg-5 col-md-12 col-sm-12">
                <h1 class="h1Orders">Заявки на обслуживание</h1>
            </div>
            <div class="col-lg-7 col-md-12 col-sm-12 text-right swOrders">
                <div id="status_filter">
                    <div id="status_switcher_all" class="switcher active" onclick="activateFilterStatus(0);filter();">
                        Все
                    </div>
                    <div id="status_switcher_not_done" class="switcher" onclick="activateFilterStatus(2);filter();">
                        В работе
                    </div>
                    <div id="status_switcher_done" style="margin-right: 22px;" class="switcher" onclick="activateFilterStatus(1);filter();">
                        Завершенные
                    </div>
                    <button class="filter"></button>
                    <button class="search"></button>
                </div>
            </div>

            <div class="col-lg-9 col-md-12  col-sm-12 ordersFilter ordersFilterRight ">
                <div id="date_filter">
                    <img src="../img/icons/calendar.png" alt="">
                    <div id="date_switcher_month" class="switcher switcherTime active" onclick="activateFilterDate(1);filter();">
                        <p>Месяц</p>
                    </div>
                    <div id="date_switcher_quart" class="switcher switcherTime" onclick="activateFilterDate(2);filter();">
                        <p>Квартал</p>
                    </div>
                    <div id="date_switcher_half_year" class="switcher switcherTime" onclick="activateFilterDate(3);filter();">
                        <p>Полугодие</p>
                    </div>
                    <div id="date_switcher_year" class="switcher switcherTime" onclick="activateFilterDate(4);filter();">
                        <p>Год</p>
                    </div>
                    <div id="date_switcher_all_year" class="switcher switcherTime" onclick="activateFilterDate(5);filter();">
                        <p>Все время</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-12 col-sm-12" style="margin-top: 13px;">
                <div class="ordersSearch ">
                    <img src="../img/icons/search-disabled.png" alt="">
                    <input type="text" id="search" name="search" сlass="num__search" placeholder="Поиск по заявкам"
                           style="margin-left: 20px;" oninput="filter();">
                </div>
            </div>

        </div>
        <table class="tbK">
            <!-- HEAD -->
            <thead class="OrdersHead sad">
            <tr>
                <td style="width: 10%;" class="ordersID" ><p class="p_tab id_p_tab"> ID
                        <img src="../img/icons/chevron-down.png" alt=""></p>
                </td>
                <td style="width: 9%;" class="crateData" >
                    <p class="p_tab "> ДАТА <img src="../img/icons/chevron-down.png" alt=""></p>
                </td>
                <td style="width: 16%;"><p class="p_tab" style="margin:auto"> СТАТУС <img src="../img/icons/chevron-down.png" alt=""></p>
                </td>
                <td style="width: 30%;" class="ordersYstroistvo"><p class="p_tab " style="margin:auto"> УСТРОЙСТВО <img
                                src="../img/icons/chevron-down.png" alt="">
                    </p></td>
                <? if ($roleId != 1) { ?>
                    <td style="width: 16%;" class="ordersCompany"><p class="p_tab" style="margin:auto"> КОМПАНИЯ <img
                                    src="../img/icons/chevron-down.png" alt=""></p></td>
                <? } ?>
                <!--                    <td><p class="p_tab" style="margin:auto"> Приоритет </p></td>-->
                <? if ($roleId >= 0) { ?>
                    <td style="width: 19%;" class="ordersIngeneer" style="width: 15%;">
                        <p class="p_tab "> ИНЖЕНЕР <img src="../img/icons/chevron-down.png" alt=""></p>
                    </td>
                <? } ?>
            </tr>
            </thead>
            <tbody class="history" id="history">
            <?
            foreach ($orders as $k => $v) {
                ?>
                <tr id="row<? echo $k; ?>"
                    data-text="<? echo $orders[$k]["company_short_name"] . ";" . $orders[$k]["order_id"] . ";" . $orders[$k]["device_model"] . ";" . $orders[$k]["device_serial_number"] . ";" . $orders[$k]["engineer_short_name"] . ";" . $orders[$k]["order_dt_creation"]  ?>"
                    data-create-dt="<? echo $creationDts[$orders[$k]['order_id']]; ?>">
                    <? if ($roleId != 1) { ?>

                    <? } ?>

                    <td id="order<? echo $k; ?>" class="nomer sad">
                        <a href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                            <div style="display: flex;">
                                <div class="orderBoll" style="background: <? echo getPriorityColor($orders[$k]['priority_id'], $orders[$k]['status_id']); ?>">
                                </div>
                                <p><? echo $orders[$k]["order_id"]; ?></p>
                            </div>
                        </a>
                    </td>
                    <td class="crateData">
                        <a  id="crateData<? echo $k; ?>"
                            href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                            <input style="margin: 0; color: black; font-weight: 500; width: 75px;" type="text" disabled value="<? echo $creationDts[$orders[$k]['order_id']];?>">
                        </a>
                    </td>
                    <td id="status<? echo $k; ?>" class="status"
                        data-status="<? echo transformStatus($orders[$k]["status_id"]); ?>">
                        <div class="dnDesc" style=" border-radius: 10px; margin-bottom: 10px;">
                            <div  class="idStatus">
                                <div id="order<? echo $k; ?>" class="nomer">
                                    <a href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                        <div style="display: flex;">
                                            <div class="orderBoll" style="background: <? echo getPriorityColor($orders[$k]['priority_id'], $orders[$k]['status_id']); ?>"></div>
                                            <p class="id"><? echo $orders[$k]["order_id"]; ?></p></div>
                                    </a>
                                </div>
                                <!--                STATUS-->
                                <div id="status<? echo $k; ?>" class="status"
                                     data-status="<? echo transformStatus($orders[$k]["status_id"]); ?>">
                                    <a class="status" href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                        <p> <? echo $orders[$k]["status_name"]; ?></p>
                                    </a>
                                </div>
                            </div>
                            <!--           COMPANY                     -->
                            <div style="width: 80%; height: 50%;">
                                <div style="display: flex;  text-align: center;">
                                    <? if ($roleId != 1) { ?>
                                        <div style="height: 22px; width: 50%; text-align: left;">
                                            <a id="company<? echo $k; ?>" class="companyMob"
                                               href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                                <? echo $orders[$k]["company_short_name"]; ?>
                                            </a>
                                        </div>
                                    <? } ?>
                                    <? if ($roleId >= 0) { ?>
                                        <!--          ENGENEER-->
                                        <div class="engineer" style="width: 50%; text-align: right;">
                                            <a style="padding: 0; margin: 0;" id="engineer<? echo $k; ?>"
                                               href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                                <? echo $orders[$k]["engineer_short_name"]; ?>
                                            </a>
                                        </div>
                                    <? } ?>
                                </div>
                                <!--                                DEVICE-->
                                <a class="device" id="device<? echo $k; ?>"
                                   href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                    <? echo $orders[$k]["device_model"] . "<span class='historySerial'> " . $orders[$k]["device_serial_number"] . "</span>"; ?>
                                </a>

                            </div>
                        </div>
                        <a class="sad" href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                            <? echo $orders[$k]["status_name"]; ?>
                        </a>
                    </td>
                    <td class="ystroistvo sad">
                        <a id="device<? echo $k; ?>"
                           href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                            <? echo $orders[$k]["device_model"] . "<span class='historySerial'> " . $orders[$k]["device_serial_number"] . "</span>"; ?>
                        </a>
                    </td>
                    <!--                    <td class="prioritet">-->
                    <!--                        <a id="priority-->
                    <? // echo $k; ?><!--" href="/order/index.php?order_id=-->
                    <? // echo $orders[$k]['order_id'] ?><!--">-->
                    <!--                            --><? // echo $orders[$k]["priority_name"]; ?>
                    <!--                        </a>-->
                    <!--                    </td>-->
                    <? if ($roleId != 1) { ?>
                        <td class="company sad">
                            <a id="company<? echo $k; ?>"
                               href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                <? echo $orders[$k]["company_short_name"]; ?>
                            </a>
                        </td>
                    <? } ?>
                    <? if ($roleId >= 0) { ?>
                        <td style="padding-top: 11px;" class="engineer d-none d-lg-block">
                            <a  id="engineer<? echo $k; ?>"
                                href="/order/index.php?order_id=<? echo $orders[$k]['order_id'] ?>">
                                <? echo $orders[$k]["engineer_short_name"]; ?>
                            </a>
                        </td>
                    <? } ?>
                </tr>
                <? }; ?>
            </tbody>
        </table>
    </div>
    <style>
        .history tr td a{
            display: block;
            width: 100%;
        }
    </style>
    <script>
        function activateFilterStatus(switcherNum) {
            var obj_all = document.getElementById("status_switcher_all");
            var obj_done = document.getElementById("status_switcher_done");
            var obj_not_done = document.getElementById("status_switcher_not_done");

            obj_all.classList.remove("active");
            obj_done.classList.remove("active");
            obj_not_done.classList.remove("active");

            if (switcherNum == 0) {
                obj_all.className += " active";
            } else if (switcherNum == 1) {
                obj_done.className += " active";
            } else if (switcherNum == 2) {
                obj_not_done.className += " active";
            }
        }

        function activateFilterDate(switcherNum) {
            var obj_month = document.getElementById("date_switcher_month");
            var obj_quart = document.getElementById("date_switcher_quart");
            var obj_half_year = document.getElementById("date_switcher_half_year");
            var obj_year = document.getElementById("date_switcher_year");
            var obj_all = document.getElementById("date_switcher_all_year");

            obj_month.classList.remove("active");
            obj_quart.classList.remove("active");
            obj_half_year.classList.remove("active");
            obj_year.classList.remove("active");
            obj_all.classList.remove("active");

            if (switcherNum == 1) {
                obj_month.className += " active";
            } else if (switcherNum == 2) {
                obj_quart.className += " active";
            } else if (switcherNum == 3) {
                obj_half_year.className += " active";
            } else if (switcherNum == 4) {
                obj_year.className += " active";
            } else if (switcherNum == 5) {
                obj_all.className += " active";
            }
        }

        function filter() {
            //alert(document.getElementById("status_filter").getElementsByClassName("active").outerHTML);
            //alert($("#status_filter .active").attr('class'));
            var n = <? echo count($orders); ?>;
            var status_filter_id = $("#status_filter .active").attr('id');
            var date_filter_id = $("#date_filter .active").attr('id');
            var searchText = document.getElementById("search").value;
            for (var i = 0; i < n; i++) {
                if (toHideRow(i, status_filter_id, date_filter_id, searchText, )) {
                    document.getElementById("row" + i).style.display = "none";
                } else {
                    //document.getElementById("row"+i).removeAttr("style");
                    $("#row" + i).removeAttr("style");
                }
            }
        }

        function toHideRow(i, status_filter_id, date_filter_id, searchText ) {
            //alert(status_filter_id + ": " + date_filter_id);
            const toHideByDateTime = !toShowByDateTime(i, date_filter_id);
            return (!toShowByStatus(i, status_filter_id)) || (!toShowByText(i, searchText) || (toHideByDateTime));
        }

        function toShowByStatus(i, status_filter_id) {
            //alert(i + ": " + status_filter_id);
            var status = document.getElementById("status" + i).getAttribute("data-status");
            if (status_filter_id.localeCompare("status_switcher_all") == 0) {
                return true;
            }
            if ((status == 1) && (status_filter_id.localeCompare("status_switcher_done") == 0)) {
                return true;
            }
            if ((status == 0) && (status_filter_id.localeCompare("status_switcher_not_done") == 0)) {
                return true;
            }
            return false;
        }

        function toShowByDateTime(i, date_filter_id) {
            // alert("Check Date: " + i + " " + date_filter_id);
            const dt_str = $("#row" + i).attr('data-create-dt');
            const dt = parseStrToDateTime(dt_str);
            const curDt = getCurDateTime();
            const delta = curDt - dt;       // количество миллисекунд
            var x = 0;
            if (date_filter_id.localeCompare("date_switcher_month") == 0) {
                x = 30;
            } else if (date_filter_id.localeCompare("date_switcher_quart") == 0) {
                x = 90;
            } else if (date_filter_id.localeCompare("date_switcher_half_year") == 0) {
                x = 180;
            } else if (date_filter_id.localeCompare("date_switcher_year") == 0) {
                x = 365;
            } else if (date_filter_id.localeCompare("date_switcher_all_year") == 0) {
                return true;
            } else {    // нештатная ситуация
                return true;
            }
            x = x * 24 * 60 * 60 * 1000; // миллисекунды
            // alert("id = " + date_filter_id + ": X = " + x + ": Delta = " + delta + ": " + (delta <= x));
            if (delta <= x) return true;
            else return false;
        }
        function toShowByText(i, textSubstr) {
            var sub_name = textSubstr.toUpperCase();
            var cur_string = document.getElementById("row" + i).getAttribute("data-text").toUpperCase();
            var isContain = cur_string.includes(sub_name);
            return isContain;
        }

        function test() {
            //console.log($("#row1 a status"));
            alert(document.getElementById("status1").innerHTML.trim());
        }
        $(document).ready(function() {
            activateFilterDate(1);
            filter();
        });
    </script>


<?php
require('../footer.php');
?>