<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
if ($roleId == 6) {
    header("Location: ../storage/");
}
$seo['title'] = 'Оформить заявку';
if (!empty($_POST)) {
    if (!((isset($_POST["descr"])) and ((strlen($_POST["descr"]) > 0)))) {
        $submitStatus = -1;
    } else if (!isset($_POST["device"])) {
        $submitStatus = -2;
    } else if (!isset($_POST["priority"])) {
        $submitStatus = -3;
    } else {
        $submitStatus = 1;
        $userId = $userdata["user_id"];
        if ($roleId == 1) {
            $companyId = $userdata["company_id"];
        } else {
            $companyId = $_POST["company"];
        }
        $deviceId = $_POST["device"];
        $priorityId = $_POST["priority"];
        $descr = $_POST["descr"];
        $descr = prepareString($descr);
        $counter = $_POST["counter"];
        //echo $userId . " ".$deviceId." ".$priorityId." ".$descr;

        //$newOrderId = genNewOrderId($userId);
        $dt = date("Y-m-d H:i:s", time());
        if (isset($counter) and !empty($counter)) {
            updateCounterInfo($companyId, $deviceId, $userId, $dt, $counter);
        }

        $orderId = getLastOrderId() + 1;
        $sql = 'insert into orders(order_id, order_dt_creation, user_id, device_unq_id, order_priority_id, order_text_request, company_id) values (' . $orderId . ', "' . $dt . '", ' . $userId . ', ' . $deviceId . ', ' . $priorityId . ', "' . $descr . '", ' . $companyId . ')';
        $result = executeInsertRequest($sql);

        addMessage($orderId, $userId, $dt, $descr);
        //$visResult = makeOrderVisibleForCompany($orderId, $companyId);
        //makeOrderVisibleForServiceManagers($orderId);
        //makeOrderVisibleForAdmins($orderId);
        //makeOrderVisibleForProjectManagers($orderId);
        //makeOrderVisibleForEngineers($orderId);
        $currentOrder = getDetailedInfoAboutOrder($orderId, False);
        try {
            $heading = "Оформлена новая заявка от {$currentOrder['company_short_name']}";
            $body = makeCreatedEmailBody($currentOrder);
            sendEmail(getProjectManagerEmail($currentOrder['company_id']), "", $heading, $body);
            sendEmail(getServiceManagerEmail(), "", $heading, $body);

            $heading = "Заявка №{$orderId} оформлена";
            $body = makeCreatedEmailBodyForClient($currentOrder);
            if ($roleId == 1) {
                $clientEmail = getUserEmail($userId);
            } else {
                $clientEmail = getUserEmailByCompany($companyId);
            }
//        $clientEmail = "gik06@yandex.ru";
            sendEmail($clientEmail, "", $heading, $body);
        } catch (Exception $e) {
            handleError(0, $e);
        }
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
$companies = getActiveCompanies(False);
$priors = getPriorities(False);
$companyId = $userdata['company_id'];
if ($roleId == 1) {
    $device = getCompanyDevices($userdata["company_id"], False);
} else {
    $device = getAllDevicesView(False);
}
?>
<style>
    .create_order_row select {
        min-width: 160px;
    }
</style>
    <div id="create-order">
        <div class="row">
            <div class="col-lg-12 h6create" >
                <h6> Новая заявка</h6>
            </div>
            <div class="col-lg-12">
                <form method="POST" action="" id="submitForm">
                    <div class="create_order_row ">
                        <? if ($roleId != 1) { ?>
                            <p>компания</p>
                            <select class="form-control" id="company" name="company">
                                <? for ($i = 0; $i < count($companies); $i++) { ?>
                                    <div class="option">  <option value="<? echo $companies[$i]['company_id']; ?>"><? echo $companies[$i]['company_short_name']; ?></option></div>
                                <? } ?>
                            </select>
                        <? } ?>
                    </div>
                    <div class="create_order_row ">
                        <p>устройство</p>
                        <input type="hidden" name="device" id="device" value="">
                        <select class="form-control name__device" id="deviceModel" name="deviceModel">
                        </select>
                    </div>
                    <div class="create_order_row">
                        <p>серийный номер</p>
                        <select class="form-control name__device" id="serialNumber" name="serialNumber">
                        </select>
                    </div>
                    <div class="create_order_row ">
                        <p>приоритет</p>
                        <select class="form-control" id="priority" name="priority">
                            <? for ($i = 0; $i < count($priors); $i++) { ?>
                                <option value="<? echo $priors[$i]['priority_id']; ?>"><? echo $priors[$i]['priority_name']; ?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="create_order_row">
                        <p>счетчик <span id="counterValue"></span></p>
                        <input type="number" name="counter" id="counterCreate" class="form-control">
                        <br/>
                    </div>

                    <div class="create_order_row " style="margin-top: 25px;">
                        <p>проблема <br> (код ошибки)</p>
                        <textarea id="descr" name="descr" cols="30" class="form-control" rows="10"
                                  placeholder="Описание (код ошибки)"></textarea>
                    </div>

                    <?php if ($roleId == 6) { ?>
                    <div class="create_order_row">
                        <video id=“preview”></video>
                        <script>
                            const Instascan = require('instascan');
                            let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
                            scanner.addListener('scan', function (content) {
                                alert(content);
                            });
                            Instascan.Camera.getCameras().then(function (cameras) {
                                if (cameras.length > 0) {
                                    scanner.start(cameras[0]);
                                } else {
                                    console.error('No cameras found.');
                                }
                            }).catch(function (e) {
                                console.error(e);
                            });
                        </script>
                    </div>
                    <?php } ?>
            </div>
            </form>
            <input class="sub_btn" value="Отправить" onclick="finalCheckCreateOrderPageAndSubmit();">
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            <? if ($submitStatus >= 1) { ?>
            showSuccessModal("Заявка успешно отправлена", "Номер заявки - <? echo $orderId; ?>");
            <? } else if (($submitStatus <= 0) and (isset($submitStatus))) { ?>
            <? $arrText = getErrorHeadingAndText($submitStatus); ?>
            showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
            <? } ?>

            $("#deviceModel").change(function () {
                //alert("Model Changed");
                //const deviceId = $("#deviceModel option:selected").attr('data-device_id');
                const deviceId = $("#deviceModel").val();
                const devicesArr = JSON.parse(devices);
                $("#serialNumber").empty();
                for (let i = 0; i < devicesArr.length; i++) {
                    //alert(devicesArr[i].device_id);
                    if (devicesArr[i].device_id.localeCompare(deviceId) == 0) {
                        //alert(devicesArr[i].device_serial_number);
                        $("#serialNumber").append($("<option></option>").text(devicesArr[i].device_serial_number));
                        //$("#device").val(devicesArr[i].device_unq_id);
                    }
                }
            });
            $("#modal-success").on("hidden.bs.modal", function () {
                window.location.href = "/orders/";
            });
            fillDevices(devices);
        });
        $('#company').on('change', function () {
            const userId = <? echo $userdata['user_id']; ?>;
            //const companyId = companySelector.options[companySelector.selectedIndex].value;
            const companyId = $("#company").val();
            $('#deviceModel').empty();
            document.getElementById("deviceModel").disabled = true;
            $.ajax({
                type: 'POST',
                url: '/php/handleGetDevicesRequest.php',
                data: {
                    'userId': userId,
                    'companyId': companyId
                },
                success: function (msg) {
                    document.getElementById("deviceModel").disabled = false;
                    devices = msg;
                    fillDevices(devices);
                    //init2(msg);
                }
            });
        });
        function fillDevices(jsonText) {
            $("#deviceModel").empty();
            $("#serialNumber").empty();
            // $("#device").empty();
            const obj = JSON.parse(jsonText);

            let unqDevices = [];
            for (let key in obj){
                const deviceModel = obj[key].device_model;
                if (unqDevices.indexOf(deviceModel) === -1) {
                    unqDevices.push(deviceModel);
                    $("#deviceModel").append($("<option></option>").text(deviceModel).val(obj[key].device_id));
                }
                //document.getElementById('device').innerHTML += '' + '<option data-counter=\''+obj[key].counter_value+'\' value="' + obj[key].device_unq_id + '">'+obj[key].device_model+' (' + obj[key].device_serial_number + ')' + '</option>';
            }
            const deviceId = $("#deviceModel").val();
            //alert(deviceId);
            for (let i in obj) {
                //alert(obj[i].device_id);
                if (obj[i].device_id.localeCompare(deviceId) === 0) {
                    //alert(obj[i].device_serial_number);
                    //$("#serialNumber").val(obj[i].device_serial_number);
                    $("#deviceModel").trigger("change");
                    break;
                }
            }
            //alert(deviceId);
        }
        function setDeviceUnqId(serialNumber) {
            const obj = JSON.parse(devices);
            for (let i in obj) {
                if (obj[i].device_serial_number.localeCompare(serialNumber) === 0) {
                    $("#device").val(obj[i].device_unq_id);
                    break;
                }
            }
        }
        function finalCheckCreateOrderPageAndSubmit() {
            const serialNumber = $("#serialNumber").val();
            setDeviceUnqId(serialNumber);
            checkCreateOrderPageAndSubmit();
        }
        let devices = '<? echo getCompanyDevices($companyId, true); ?>';
    </script>
<?php
require('../footer.php');
?>