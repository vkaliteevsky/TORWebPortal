<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
if ($roleId == 6) {
    header("Location: ../storage/");
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/php/handleUpdateOrder.php"); // определяет $submitStatus

$orderId = $_GET['order_id'];
$seo['title'] = 'Заявка №' . $orderId;

$isOrderVisible = isOrderVisibleForUser($orderId, $userdata["user_id"]);
if (!$isOrderVisible) {
    header("Location: /orders/");
}
$orderstatuses = getStatuses(False);

$currentOrder = getOrderInfo($orderId, False);
$userCreated = getCreatedUserInfo($orderId);
$statusId = $currentOrder['order_status_id'];
$isActiveEngineer = ($currentOrder['user_id_engineer'] == $userdata['user_id']);

//$thisCompanyId = getCompanyIdByOrderId($orderId);
$thisCompanyId = $currentOrder["company_id"];
$thisCompanyInfo = getCompanyInfo($thisCompanyId, False);
$devices = getCompanyDevices($thisCompanyId, False);
$statuses = getStatuses(False);
$priors = getPriorities(False);
//$engineers = getEngineersByOrderId($orderId, False);
$allEngineers = getAllActiveEngineers(False);
//$managerText = getManagerText($orderId);

$devicesIndex = findValueIn2Array($devices, $currentOrder["device_unq_id"], 'device_unq_id');
if ($devicesIndex == -1) {
    echo "Возникла ошибка order. Невозможно найти device_unq_id";
    $devicesIndex = 0;
}

$statusIndex = findValueIn2Array($statuses, $currentOrder['order_status_id'], 'status_id');
if ($statusIndex == -1) {
    echo "Возникла ошибка order. Невозможно найти status_id";
    $statusIndex = 0;
}

$priorIndex = findValueIn2Array($priors, $currentOrder['order_priority_id'], 'priority_id');
if ($priorIndex == -1) {
    echo "Возникла ошибка order. Невозможно найти priority_id";
    $priorIndex = 0;
}

$engineerIndex = findValueIn2Array($allEngineers, $currentOrder['user_id_engineer'], 'user_id'); // может быть равен -1 (если инженер не назначен)
//$company_devices = getCompaniesDevices($company_devices, False);
$linkedOrders = getLinkedOrders($orderId, False);
$managerText = $currentOrder["order_inner_text"];
$clientText = $currentOrder["order_text_request"];
$engineerCloseText = $currentOrder["order_text_response"];
$userId = $userdata["user_id"];
$toContinueWork = $currentOrder["order_to_continue_work"];
$date_create = $currentOrder["order_dt_creation"];
//$date_acceptance = $currentOrder["order_dt_creation"];
//$date_executed = $currentOrder["order_dt_creation"];
//$toShowSuccessModal = $_POST["toShowSuccessModal"];
$deviceId = $devices[$i]['device_id'];
$engineerId = $orders[$i]['user_id_engineer'];
$statusesId = $orders[$i]['status_id'];
$EngineerArrDt = $currentOrder['engineer_arrival_dt'];

$assignedEngineerId = $devices[$devicesIndex]['engineer_id'];
if (!empty($assignedEngineerId)) {
    $assignedEngineerIndex = findValueIn2Array($allEngineers, $assignedEngineerId, 'user_id');
}
$counters = getCountersValues($thisCompanyId);
$curCounterValue = getCounterValue($thisCompanyId, $devices[$devicesIndex]['device_unq_id']);
$possibleStatuses = mapPossibleStatuses($roleId, $statusId);    // одномерный массив, содержащий список возможных статусов, в который может быть переведена заявка

$createdDt = getCreatedDateTime($orderId);
if (empty($createdDt)) $createdDt = "-";
$inWorkDt = getInWorkDateTime($orderId);
if (empty($inWorkDt)) $inWorkDt = "-";
$executedDt = getExecutedDateTime($orderId);
if (empty($executedDt)) $executedDt = "-";

$changedDetails = getChangedDetails($orderId);

function isEngineerSelected($i, $engineerIndex, $assignedEngineerIndex)
{
    if (empty($assignedEngineerIndex)) {
        return ($i == $engineerIndex);
    } else {    // нечто записано в $assignedEngineerIndex
        if (($engineerIndex == -1) and ($i == -1)) return False;
        return ($assignedEngineerIndex == $i);
    }
}
$chatMessages = getAllMessages($orderId);
require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
<!--    --><?// if ($roleId == 5) print_r($chatMessages); ?>
    <form action="" method="POST" id="updateForm">
        <input id="userId" name="userId" type="hidden" value="<? echo $userdata["user_id"]; ?>">
        <input id="orderId" name="orderId" type="hidden" value="<? echo $orderId; ?>">
        <input id="cancelFlag" name="cancelFlag" type="hidden" value="0">
        <? if ($roleId > 1) { ?>
        <input id="sparesInput" name="sparesInput" type="hidden" value="">
        <? } ?>
        <div id="updateFormCont">

            <div class="row">
                <div class="col-lg-12 orderH3Teg" style="display: flex; margin-bottom: 28px;">
                    <a href="/orders/"><img
                                style="margin-top: 5px; position: relative; left: -30px; margin-right: -30px;"
                                src="https://img.icons8.com/ios/25/000000/less-than.png"> </a>
                    <h3>Заявка <span class="NumOrder"> <? echo $currentOrder['order_id']; ?> </span></h3>
                    <? if (toShowCompany($roleId, $statusId)) { ?>
                        <div class="companyColor">
                            <input type="text" name="companyName" id="companyName"
                                   value="<? echo $thisCompanyInfo[0]['company_short_name']; ?>" disabled>
                        </div>
                    <? } ?>

                </div>

                <div class="mobTime">
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="col-sm-4">
                            <ul>
                                <li><p style="width: 100%!important;">СОЗДАНА</p></li>
                                <li><? echo $createdDt; ?></li>
                            </ul>
                        </div>
                    <? } ?>
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="col-sm-4">
                            <ul>
                                <li><p style="width: 100%!important;">В РАБОТЕ С</p></li>
                                <li> <? echo $inWorkDt; ?></li>
                            </ul>
                        </div>
                    <? } ?>
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="col-sm-4">
                            <ul>
                                <li><p style="width: 100%!important;">ЗАКРЫТА</p></li>
                                <li> <? echo $executedDt; ?></li>
                            </ul>
                        </div>
                    <? } ?>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <div class=" textarea">
                        <p style="margin-top: 3px;">Статус</p>
                        <select name="status" class="form-control statusIndex"
                                id="status" <? if (!isStatusEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?>>
                            <? for ($i = 0; $i < count($statuses); $i++) { ?>
                                <? if (in_array($statuses[$i]['status_id'], $possibleStatuses)) { ?>
                                    <option value=" <? echo $statuses[$i]['status_id']; ?>" <? if ($i == $statusIndex) echo "selected"; ?>><? Echo  $statuses[$i]['status_name']; ?></option>
                                <? } ?>
                            <? } ?>
                        </select>
                    </div>

                    <? if (toShowPriority($roleId, $statusId)) { ?>
                        <div class=" textarea">

                            <p>Приоритет</p>
                            <div class="orderBoll" id="orderBoll"
                                 style="background: <? echo getPriorityColor($priors[$priorIndex]['priority_id'], $statusId); ?>;"></div>
                            <select name="priority" class="form-control prior_name"
                                    style="color: <? echo getPriorityColor($priors[$priorIndex]['priority_id'], $statusId); ?>; "

                                    id="priority" <? if (!isPriorityEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?>>
                                <? for ($i = 0; $i < count($priors); $i++) { ?>
                                    <option data-color="<? echo getPriorityColor($priors[$i]['priority_id'], $statusId); ?>"
                                            style="color: <? echo getPriorityColor($priors[$i]['priority_id'], $statusId); ?>;"
                                            value="<? echo $priors[$i]['priority_id']; ?>" <? if ($priorIndex == $i) echo "selected"; ?>><? echo $priors[$i]['priority_name']; ?></option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                    <? if (toShowDevice($roleId, $statusId)) { ?>
                        <div class=" textarea">
                            <p>Модель</p>
                            <select name="device" class="form-control statusIndex ModelSelect"
                                    id="device" <? if (!isDeviceEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?>>
                                <? for ($i = 0; $i < count($devices); $i++) { ?>
                                    <option data-default-engineer="<? echo $devices[$i]['user_short_name']; ?>"
                                            data-place="<? echo $devices[$i]['device_place']; ?>"
                                            data-address="<? echo $devices[$i]['device_address']; ?>"
                                            data-serial-number="<? echo $devices[$i]['device_serial_number']; ?>"
                                            data-counter="<? echo findCounterValueByDevice($counters, $devices[$i]['device_unq_id']); ?>"
                                            data-contact="<? echo $devices[$i]['contact_short_name']; ?>"
                                            value="<? echo $devices[$i]['device_unq_id']; ?>" <? if ($devicesIndex == $i) echo "selected"; ?>><? echo $devices[$i]["device_model"]; ?></option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>

                    <? if (toShowDevice($roleId, $statusId)) { ?>
                        <div class=" textarea">
                            <p class="p_checkbox" style="width: 31%;">серийный номер</p>
                            <h6 id="device_serial_number" class="serialNumber"
                                style="color: #9b9b9b; font-size: 14px; font-weight: normal;margin-top: -1px;"><? echo $devices[$devicesIndex]['device_serial_number']; ?>
                            </h6>
                        </div>
                    <? } ?>
                    <div class="OrderCounter textarea">
                        <? $curCounterValueText = (empty($curCounterValue) ? "" : " (" . $curCounterValue . ")"); ?>
                        <p style="width: 31% ;" class="w35pro">Счетчик<span
                                    id="counterValue" class="counterChange"><? echo $curCounterValueText; ?></span></p>
                        <input type="number" name="counter" id="counter" class="form-control counter" style="background: none;"
                               value="" <? if (!isCounterEnabled($roleId, $statusId)) echo "disabled"; ?>>
                    </div>
<!--                    --><?// if (toShowClientText($roleId, $statusId)) { ?>
<!--                        <div class=" textarea twoLine">-->
<!--                            <p style="width: 30% ;margin-top: 12px;margin-left: 0;margin-right: 0;">ПРОБЛЕМА</p>-->
<!--                            <textarea style="width: 70%; margin-left: 0!important;" name="clientText"-->
<!--                                      id="note"  --><?// if (!isClientTextEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?><!-- >--><?// echo $clientText; ?><!--</textarea>-->
<!---->
<!--                        </div>-->
<!--                    --><?// } ?>
                    <? if (toShowEngineer($roleId, $statusId) && ($statusesId !== 1)) { ?>
                        <div class=" textarea">
                            <? $eText = ($devices[$devicesIndex]['user_short_name'] == "") ? "" : ' (' . $devices[$devicesIndex]['user_short_name'] . ')'; ?>
                            <p style="margin-top:4px;" class="placeOrder">Ответственный инженер<br><span
                                        id="defaultEngineer" class="otvetEngeneer"><? echo $eText; ?></span></p>
                            <select name="engineer" class="form-control statusIndex statusIndex2" style="margin-top: -8px; "
                                    id="engineer" <? if (!isEngineerEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?>>
                                <option value="-1" <? if ($engineerIndex == -1) echo "checked"; ?>>Не назначен</option>
                                <? for ($i = 0; $i < count($allEngineers); $i++) { ?>
                                    <option value="<? echo $allEngineers[$i]['user_id']; ?>" <? if ($i == $engineerIndex) echo "selected"; ?>><? echo $allEngineers[$i]['user_short_name']; ?></option>
                                <? } ?>
                            </select>
                        </div>
                    <? } ?>
                    <div class="OrderCounter textarea ">
                        <p style="width: 31%;">Дата приезда инженера</p>
                        <input type="text" name="arrival_dt" id="arrival_dt" class="form-control datepicker-here" value="<? echo ($EngineerArrDt == 0) ? "-" : $EngineerArrDt; ?>"  <? if (!isArrivalEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?>>
                    </div>
                    <!--
                <? if (toShowCompany($roleId, $statusId)) { ?>
                    <div class="textarea">
                        <p class="">Телефон</p>
                        <h6 style="  margin-top: 9px;"><? echo "+7 (999) 123-45-67"; ?></h6>
                    </div>
                <? } ?>
                -->

<!--                    --><?// if (toShowAddress($roleId, $statusId)) { ?>
<!--                        <div class="textarea" style="margin-top: 22px;">-->
<!--                            <p class="p_checkbox placeOrder" style=" width: 31%;">АДРЕС</p>-->
<!--                            <div style="display: block;">-->
<!--                                <h5 id="addressDevice" class="mt5 adresDevicePlace"-->
<!--                                    style="width: 250px; font-size: 14px;color: black;">--><?// echo $devices[$devicesIndex]['device_address']; ?><!--</h5>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    --><?// } ?>
<!--                    --><?// if (toShowPlace($roleId, $statusId)) { ?>
<!--                        <div class=" textarea ">-->
<!--                            <p class="p_checkbox placeOrder placeDesc"  style=" width: 31%!important;">Местонахождение</p>-->
<!--                            <p class="p_checkbox placeOrder placeMob"  style=" width: 31%; ">Мест-ние</p>-->
<!--                            <div style="display: block;">-->
<!--                                <h5 class="mt15" id="placeDevice"-->
<!--                                    style="width: 250px; font-size: 14px; color: black; margin-top: -2px !important;">--><?// echo $devices[$devicesIndex]['device_place']; ?><!--</h5>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    --><?// } ?>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="textarea sad">
                            <p style="width: 31%;">создана </p>  <h6
                                    style="margin-top: 0px!important;"><? echo $createdDt; ?></h6>
                        </div>
                    <? } ?>
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="textarea sad">
                            <p style="width: 31%;">принята в работу </p>     <h6
                                    style="margin-top: 0px!important;"><? echo $inWorkDt; ?></h6>
                        </div>
                    <? } ?>
                    <? if (toShowDates($roleId, $statusId, $isActiveEngineer)) { ?>
                        <div class="textarea sad">
                            <p style="width: 31%;">закрыта </p>   <h6
                                    style="margin-top: -1px!important;"><? echo $executedDt; ?></h6>
                        </div>
                    <? } ?>
                    <? if (toShowContactPerson($roleId, $statusId)) { ?>
                        <div class=" textarea">
                            <p style="width: 31%;">Контактное лицо</p>
                            <h5 style="" id="contactName" class="dataContact"><? echo $devices[$i]['contact_short_name']; ?></h5>
                        </div>
                    <? } ?>
                    <? if (toShowInitiator($roleId, $statusId)) { ?>
                    <div class="textarea">
                        <p style="width: 31%;">Инициатор</p>
                        <h5 id="contactName" class="dataContact"><? echo $userCreated['user_short_name']; ?></h5>
                    </div>
                    <? } ?>
                    <? if (toShowAddress($roleId, $statusId)) { ?>
                        <div class="textarea">
                            <p style="width: 31%;">Адрес</p>
                            <h5 id="contactName" class="dataContact"><? echo $devices[$devicesIndex]['device_address']; ?></h5>
                        </div>
                    <? } ?>
                    <? if (toShowPlace($roleId, $statusId)) { ?>
                        <div class="textarea">
                            <p style="width: 31%;">Местонахождение</p>
                            <h5 id="contactName" class="dataContact"><? echo $devices[$devicesIndex]['device_place']; ?></h5>
                        </div>
                    <? } ?>
<!--                    --><?// if (toShowEngineerComment($roleId, $statusId)) { ?>
<!--                        <div class=" textarea twoLine">-->
<!--                            <p style="width: 31%;margin-top: 12px;">Комментарий инженера</p>-->
<!--                            <textarea style="width: 70%; margin-left: 0!important;" name="engineerResponse"-->
<!--                                      id="note1" --><?// if (!isEngineerCommentEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?><!-- >--><?// echo $engineerCloseText; ?><!--</textarea>-->
<!--                        </div>-->
<!--                    --><?// } ?>
<!--                    --><?// if (toShowManagerComments($roleId, $statusId)) { ?>
<!--                    <div class=" textarea twoLine">-->
<!--                        <p style="width: 31%;margin-top: 12px;">Комментарий Менеджера</p>-->
<!--                        <textarea style="width: 70%; margin-left: 0!important;" name="managerText"-->
<!--                                  id="note2" --><?// if (!isManagerCommentsEnabled($roleId, $statusId, $isActiveEngineer)) echo "disabled"; ?><!-- >--><?// echo $managerText; ?><!--</textarea>-->
<!--                    </div>-->
<!--                    --><?// } ?>
                </div>
            </div>
    </form>
<br>
                <? if ($roleId >= 1) { ?>
                <div class="row mb-4">
                    <div class="chat">
                        <div class="chat__title">Чат</div>
                        <div class="chatList" id="chatListId">
                            <? for ($i = 0; $i < count($chatMessages); $i++) {
                                $msgObj = $chatMessages[$i];
                            ?>
                            <div class="chatList__item">
                                <div class="chatList__itemText">
                                    <? echo $msgObj['text']; ?>
                                </div>
                                <div class="chatList__itemInfo">
                                    <div class="chatList__itemInfoAccent">
                                        <? echo $msgObj['user_short_name']; ?>
                                    </div>
                                    <? echo $msgObj['dt']; ?>
                                </div>
                            </div>
                            <? } ?>
                        </div>
                        <div class="chatTextContainer">
                            <textarea id="newMessage" placeholder="Напишите свое сообщение здесь" class="chatTextContainer__textarea"></textarea>
                            <button type="button" class="chatTextContainer__button" onclick="sendChatMessage();">
                                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m26.07 3.996a2.974 2.974 0 0 0 -.933.223h-.004c-.285.113-1.64.683-3.7 1.547l-7.382 3.109c-5.297 2.23-10.504 4.426-10.504 4.426l.062-.024s-.359.118-.734.375a2.03 2.03 0 0 0 -.586.567c-.184.27-.332.683-.277 1.11.09.722.558 1.155.894 1.394.34.242.664.355.664.355h.008l4.883 1.645c.219.703 1.488 4.875 1.793 5.836.18.574.355.933.574 1.207.106.14.23.257.379.351a1.119 1.119 0 0 0 .246.106l-.05-.012c.015.004.027.016.038.02.04.011.067.015.118.023.773.234 1.394-.246 1.394-.246l.035-.028 2.883-2.625 4.832 3.707.11.047c1.007.442 2.027.196 2.566-.238.543-.437.754-.996.754-.996l.035-.09 3.734-19.129c.106-.472.133-.914.016-1.343a1.807 1.807 0 0 0 -.781-1.047 1.872 1.872 0 0 0 -1.067-.27zm-.101 2.05c-.004.063.008.056-.02.177v.011l-3.699 18.93c-.016.027-.043.086-.117.145-.078.062-.14.101-.465-.028l-5.91-4.531-3.57 3.254.75-4.79 9.656-9c.398-.37.265-.448.265-.448.028-.454-.601-.133-.601-.133l-12.176 7.543-.004-.02-5.836-1.965v-.004l-.015-.003a.27.27 0 0 0 .03-.012l.032-.016.031-.011s5.211-2.196 10.508-4.426c2.652-1.117 5.324-2.242 7.379-3.11 2.055-.863 3.574-1.496 3.66-1.53.082-.032.043-.032.102-.032z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <? } ?>
                <div class="row orderDocsDetails">
                    <? if (($roleId > 0)) { ?>
                    <div class="col-lg-6 col-12">
                        <div class="detailsHeading">
                            <p>Документы</p>
                            <img src="../img/icons/plus.png" id="imgAddPhoto" />
                            <img style="opacity: 0.5;" src="../img/icons/download_icon.png" id="imgDownloadAllDocs" onclick="downloadAllDocs();" />
                        </div>
                        <form action="../php/dropzone/dropFiles.php" method="post" class="dropzone" id="myDropzone">
                            <div class="dz-message" data-dz-message></div>
                        </form>
                    </div>
                    <? } ?>
                    <? if ($roleId > 1) { ?>
                    <div class="col-lg-6 col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="detailsHeading">Замена деталей</div>
                            </div>
                        </div>
                        <div class="row detailsTableHeading" style="margin-top: 23px;">
                            <div class="col-1"></div>
                            <div class="col-4">НОМЕР</div>
                            <div class="col-5">НАЗВАНИЕ</div>
                            <div class="col-2">КОЛ-ВО</div>
                        </div>
                        <div id="detailRows" class="detailsTableRows">
                            <? for ($i = 0; $i < count($changedDetails); $i++) { ?>
                            <? $curDetail = $changedDetails[$i]; ?>
                            <div data-type="detailRow" class="row" data-spare_id="<? echo $curDetail['spare_id'] ?>">
                                <div class="col-1 text-right padding-0">
                                    <span class="minusSpare" onclick="deleteSpare(this);">
                                        <img src="../img/icons/minus.svg" >
                                    </span>
                                </div>
                                <div class="col-4">
                                    <input data-type="spareCode" size="10" type="text" value="<? echo $curDetail['spare_code']; ?>" oninput="fillSpareNames();">
                                </div>
                                <div class="col-5">
                                    <p data-type="spareName"><? echo $curDetail['spare_name']; ?></p>
                                </div>
                                <div class="col-2">
                                    <input data-type="spareAmount" size="2" type="text" value="<? echo $curDetail['amount']; ?>">
                                </div>
                            </div>
                            <? } ?>
                        </div>
                        <div class="row detailsAddDetail">
                            <div class="col-1 padding-0 text-right">
                                <span class="plusSpare" onclick="addDetail();"><img src="../img/icons/plus.svg" /></span>
                            </div>
                            <div class="col-11">
                                <p onclick="addDetail();">Добавить деталь</p>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>

            <div style="width: 100%; text-align: center;">
                <br>
                <button type="button" class="orderSend" onclick="checkAndSubmit(<? echo $roleId; ?>);">Сохранить</button>
                <br><br>
                <? if (toShowCancelOrderButton($roleId, $statusId)) { ?>
                    <button type="button" class="btn orderCancelButton btn_close" onclick="cancelOrder();">Отменить заявку</button>
                <? } ?>
                <? if (canDeleteOrders($roleId, $statusId)) { ?>
                    <button type="button" class="btn orderCancelButton btn_close" onclick="deleteOrder();">Удалить заявку</button>
                <? } ?>
            </div>
        </div>


<br><br>
    <style>
        #updateFormCont .errorCounter {
            color: red !important;
            border-bottom: 1px solid red !important;
        }
        .errorSpare {
            border-bottom: 2px solid red !important;
        }
        .plusSpare img {
            margin-top: 1px;
            max-width: 13px;
        }
        .plusSpare:hover {
            cursor: pointer;
        }
        .minusSpare {
            color: #9b9b9b;
            font-size: 13px;
            border-bottom: 2px solid #fff;
        }
        .minusSpare:hover {
            cursor: pointer;
        }
        /* ------------------------------------ DROP ZONE -------------------------------- */
        .dz-preview {
            margin-left: 0px;
        }
        .dz-remove {
            color: #9b9b9b;
        }
        .dz-remove:hover {
            color: #000;
            text-decoration: none;
        }
        .dz-image img {
            width: 120px;
            height: 106px;
        }
        .dz-image:hover {
            cursor: pointer;
        }
        .dz-size {
            display: none;
        }
    </style>
    <script>

//
      $(document).ready(function() {
          $('select').niceSelect();
      });

        (function($){

            $('#arrival_dt').inputmask("datetime",{
                mask: "y-2-1 h:s",
                placeholder: "____-__-__ __:__",
                leapday: "-02-31" ,
                separator: "-",
                alias: "yyyy-mm-dd"
            });


        })(jQuery)

        xCal.all("datepicker");

        autosize(document.getElementById("note"));
        autosize(document.getElementById("note1"));
        autosize(document.getElementById("note2"));
        <? if (isset($submitStatus)) { ?>
        $(document).ready(function () {
            <? if ($submitStatus >= 1) { ?>
            showSuccessModal("Заявка <? echo $orderId; ?> успешно обновлена", null);
            <? } else if ($submitStatus <= 0) { ?>
            <? $arrText = getErrorHeadingAndText($submitStatus); ?>
            showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
            <? } ?>
        });
        <? } ?>

        Dropzone.autoDiscover = false;
        $(document).ready(function () {
            document.getElementById("device").addEventListener("change", function () {
                const selectedDevice = $(this).children("option:selected");
                const place = selectedDevice.attr('data-place');
                var placeObj = document.getElementById('placeDevice');
                if (placeObj) placeObj.innerHTML = place;

                const address = selectedDevice.attr('data-address');
                var addressObj = document.getElementById('addressDevice');
                if (addressObj) addressObj.innerHTML = address;

                var defaultEngineer = selectedDevice.attr('data-default-engineer');
                if (defaultEngineer) defaultEngineer = '(' + defaultEngineer + ')';
                var engineerObj = document.getElementById('defaultEngineer');
                if (engineerObj) engineerObj.innerHTML = defaultEngineer;

                const serialNumber = selectedDevice.attr('data-serial-number');
                var serialObj = document.getElementById('device_serial_number');
                if (serialObj) serialObj.innerHTML = serialNumber;

                var counterValue = selectedDevice.attr('data-counter');
                if (counterValue) counterValue = ' (' + counterValue + ')';
                var counterObj = document.getElementById('counterValue');
                if (counterObj) counterObj.innerHTML = counterValue;

                var contactName = selectedDevice.attr('data-contact');
                var contactObj = document.getElementById('contactName');
                if (contactObj) contactObj.innerHTML = contactName;
            });
            document.getElementById("priority").addEventListener("change", function () {
                // alert("Change");
                const selectedPrior = $(this).children("option:selected");
                const priorColor = selectedPrior.attr('data-color');
                document.getElementById("priority").style.color = priorColor;

                document.getElementById("orderBoll").style.background = priorColor;
            });
        });

        try {
            var sparesInfo = '<?php echo getSparesJSON(); ?>';
            var sparesObj = JSON.parse(sparesInfo);
        } catch (errObj) {
            alert(sparesObj);
        }
        function addDetail() {
            let rowHTML = "<div data-type='detailRow' class='row'>\n";
            rowHTML += "<div class=\"col-1 text-right padding-0\">";
            rowHTML += "<span class=\"minusSpare\" onclick=\"deleteSpare(this);\"><img src=\"../img/icons/minus.svg\" ></span>";
            rowHTML += "</div>";
            rowHTML += "<div class=\"col-4\">\n";
            rowHTML += "<input data-type=\"spareCode\" size=\"10\" type=\"text\" value=\"\" oninput=\"fillSpareNames();\">\n";
            rowHTML += "</div>";
            rowHTML += "<div class=\"col-5\">";
            rowHTML += "<p data-type=\"spareName\"></p>";
            rowHTML += "</div>";
            rowHTML += "<div class=\"col-2\">";
            rowHTML += "<input data-type=\"spareAmount\" size=\"2\" type=\"text\" value=\"\">";
            rowHTML += "</div>";
            rowHTML += "</div>";
            $("#detailRows").append(rowHTML);
        }
        function checkSpares() {
            let isOk = true;
            $("#detailRows .row").each(function() {
                const spareCodeSelector = $(this).find("[data-type=spareCode]");
                const spareCode = spareCodeSelector.val();
                const index = findSpareIdByCode(spareCode);
                if (index == -1) {
                    spareCodeSelector.addClass('errorSpare');
                    isOk = false;
                } else {
                    spareCodeSelector.removeClass('errorSpare');
                    //const spareName = sparesObj[index].spare_name;
                    //$(this).find("[data-type=spareName]).html(spareName);
                }

                const spareAmountSelector = $(this).find("[data-type=spareAmount]");
                const spareAmount = spareAmountSelector.val();
                if (spareAmount.length <= 0) {
                    spareAmountSelector.addClass('errorSpare');
                    isOk = false;
                } else {
                    spareAmountSelector.removeClass('errorSpare');
                }
            });
            return isOk;
        }
        function fillSpareNames() {
            $("#detailRows .row").each(function() {
                const spareCodeSelector = $(this).find("[data-type=spareCode]");
                const spareCode = spareCodeSelector.val();
                const index = findSpareIdByCode(spareCode);
                let spareName = "";
                let spareId = "";
                if (index != -1) {
                    spareName = sparesObj[index].spare_name;
                    spareId = sparesObj[index].spare_id;
                }
                $(this).find("[data-type=spareName]").html(spareName);
                $(this).attr('data-spare_id', spareId);
            });
        }
        function findSpareIdByCode(spareCode) {
            let index = -1;
            for (let i = 0; i < sparesObj.length; i++) {
                if (!sparesObj[i].spare_code.localeCompare(spareCode)) {
                    index = i; break;
                }
            }
            return index;
        }
        function makeDetailsJSON() {
            let arr = [];
            $("#detailRows .row").each(function(i) {
                const spareCode = $(this).find("[data-type=spareCode]").val();
                //const spareName = $(this).find("[data-type=spareName]").html();
                const amount = $(this).find("[data-type=spareAmount]").val();
                let spareId = $(this).attr('data-spare_id');
                if (!spareId) spareId = -1;
                arr[i] = {spareId: spareId, spareCode: spareCode, amount: amount};
            });
            const jsonText = JSON.stringify(arr);
            $("#sparesInput").val(jsonText)
            return jsonText;
        }
        function deleteSpare(obj) {
            $(obj).parent().parent().remove();
        }
        function submitFilesForm() {
            $("#myDropzone").submit();
        }
        function openDropzone() {
            $('#myDropzone').get(0).dropzone.hiddenFileInput.click();
        }

        $("#myDropzone").dropzone({
            url: "../php/dropzone/dropFiles.php",
            method: "post",
            addRemoveLinks: true,
            dictRemoveFile: "Удалить",
            paramName: "imgs",
            acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
            clickable: "#imgAddPhoto",
            autoProcessQueue: true,
            init: function() {
                myDropzone = this;
                $.ajax({
                    url: "../php/dropzone/getFileList.php",
                    type: "post",
                    data: {
                        orderId: <? echo $orderId; ?>
                    },
                    dataType: "json",
                    success: function(response) {
                        $.each(response, function(key,value) {
                            let mockFile = { name: value.name, size: value.size };
                            myDropzone.emit("addedfile", mockFile);
                            myDropzone.emit("thumbnail", mockFile, value.path);
                            myDropzone.emit("complete", mockFile);
                        });
                        let docs = document.getElementsByClassName("dz-image");
                        for (let i = 0; i < docs.length; i++) {
                            docs[i].addEventListener('click', function() {
                                const docName = $(this).children('img').attr('alt');
                                const docId = docName.split(".")[0].split("_")[1];
                                let orderId = <?php echo $orderId; ?>;
                                // alert(docId + ":" + orderId);
                                window.location.href = "../php/handleDownloadDoc.php?orderId=" + orderId+"&docId=" + docId;
                            }, false);
                        }
                    }
                });
            },
            removedfile: function(file) {
                let name = file.name;
                $.ajax({
                    url: "../php/dropzone/deleteFile.php",
                    type: 'POST',
                    data: {
                        name: name,
                        orderId: <? echo $orderId; ?>
                    },
                    success: function(msg) {
                        // alert(msg);
                    }
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            }
        });
        function downloadAllDocs() {
            $.ajax({
                    url: "../php/handleGetAllDocuments.php?orderId=<?php echo $orderId; ?>",
                    type: 'GET',
                    success: function (result) {
                        if (result.length >= 4) {
                            let getURL = "../php/zipAndDownload.php?imgNames=" + result;
                            // $(".dz-filename").each(function () {
                            //     getURL = getURL + $(this).find("span").html() + ";";
                            // });
                            // getURL = getURL.substring(0, getURL.length - 1);
                            window.location.href = getURL;
                        }
                    }, error: function (result) {
                        alert("Не удается получить список документов. Просьба связаться с системным администратором: " + result);
                    }
            });
        }
        fillSpareNames ();
        //alert(makeDetailsJSON());
    </script>
    <script src="/js/chat.js"></script>
<?php
require('../footer.php');
?>