/* ----------------------------- For /order/ ---------------------------- */
function checkDataBeforeSubmitUpdateOrder(roleId) {
    const isEngineerOk = checkStatusAndEngineer();
    const isCounterOk = checkCounterOnOrderPage(roleId);
    const isArrivalDtOk = checkArrivalDtOnOrderPage();
    const isCheckSparesDetailsOk = checkSpares();
    // alert(isEngineerOk + " ! " + isCounterOk);
    return isEngineerOk && isCounterOk && isArrivalDtOk && isCheckSparesDetailsOk;
}
function checkArrivalDtOnOrderPage() {
    const statusId = $("#status").children("option:selected").attr('value');
    const arrivalDt = $("#arrival_dt").val();
    const isArrivalEmpty = (arrivalDt.length <= 0);
    if ((statusId > 2) && (isArrivalEmpty)) {
        $("#arrival_dt").addClass("engineerError");
        return false;
    } else {
        $("#arrival_dt").removeClass("engineerError");
        return true;
    }
    // return false;
    // alert("StatusId = " + statusId + ": ArrivalDt = " + arrivalDt);
}
function isMobile() {
    return (window.screen.width <= 768);
}
function deleteOrder() {
    const orderId = $("#orderId").val();
    const userId = $("#userId").val();
    $("#modal-success").on("hidden.bs.modal", function () {
        window.location.href = "/orders/";
    });
    $.ajax({
        type: 'POST',
        url: '/php/handleDeleteOrder.php',
        data: {
            'orderId': orderId,
            'userId': userId
        },
        success: function(msg){
            //alert("Server Response:\n" + msg);
            if (msg.localeCompare("200")==0) {
                showSuccessModal("Заявка " + orderId + " удалена", null);
            } else {
                showFailModal("Возникла ошибка при удалении заявки", "Обратитесь к службе технической поддержки");
            }
        }
    });
}
function checkStatusAndEngineer() {
    var statusId = $("#status").children("option:selected").attr('value');
    var engineerId = $("#engineer").children("option:selected").attr('value');

    if (((statusId >= 2) && (engineerId == -1)) || ((statusId == 1) && (engineerId != -1))) {
        showEngineerError();
        return false;
    } else {
        hideEngineerError();
        return true;
    }
}
function hasOnlyDigits(val) {
    return /^\d+$/.test(val);
}
function showEngineerError() {
    $("#engeneerId").addClass("engineerError");
}
function hideEngineerError() {
    $("#engeneerId").removeClass("engineerError");
}

function checkCounterOnOrderPage(roleId) {
    var value = $("#counter").val();
    var lastValue = $("#device").children("option:selected").attr('data-counter');
    if (roleId == 5) {
        return true;
    }
    if (parseInt(value) < parseInt(lastValue)) {
        showErrorCounterOnOrderPage();
        return false;
    } else {
        hideErrorCounterOnOrderPage();
        return true;
    }
}
function showErrorCounterOnOrderPage() {
    $("#counter").addClass("errorCounter");
}
function hideErrorCounterOnOrderPage() {
    $("#counter").removeClass("errorCounter");
}
function checkAndSubmit(roleId) {
    const isOk = checkDataBeforeSubmitUpdateOrder(roleId);
    if (isOk) {
        enableInputsOnPageOrder();
        makeDetailsJSON();
        submitUpdateOrder();
    }
}
function enableInputsOnPageOrder() {
    $("#device").prop('disabled', false);
    $("#note").prop('disabled', false);
}
function submitUpdateOrder() {
    $('#updateForm').submit();
}
function cancelOrder() {
    // $("input").each(function(){
    //     this.value = "";
    // });
    document.getElementById("cancelFlag").value = 1 ;
    submitUpdateOrder();
}
/* -------------------------------------- Send Counters ------------------------------- */
function sendCounterInfo(roleId) {
    //alert(userId + ": " + deviceUnqId + ": " + counterValue + ": " + companyId);
    // alert(JSON.stringify(getCountersArray()));
    // return;
    const deviceCounters = getCountersArray();
    const n = deviceCounters.length;
    // alert(JSON.stringify(deviceCounters));
    var toSend = (n <= 0) ? false : true;
    if ((roleId == 5) && (toSend)) {
        const countersJSON = JSON.stringify(deviceCounters);
        document.getElementById("deviceCountersJSON").value = countersJSON;
        document.getElementById("myForm").submit();
        return;
    }
    // alert("n = " + n);
    for (var i = 0; i < n; i++) {
        var j = deviceCounters[i]['i'];
        // alert("Checking counter j = " + j);
        const checkCode = checkCounter(j);
        if (checkCode != 0) {   // error
            // alert(i + " wrong");
            toSend = false;
            showCounterError(j);
        } else {
            // alert(i + " correct");
            showCounterNoError(j);
        }
    }
    if (!toSend) {
        return;
    }
    const countersJSON = JSON.stringify(deviceCounters);
    document.getElementById("deviceCountersJSON").value = countersJSON;
    document.getElementById("myForm").submit();
    // alert(countersJSON);
    /*
    $.ajax({
        type: 'POST',
        url: '/php/handleCounterRequest.php/',
        data: {
            'userId': userId,
            'deviceCountersJSON': countersJSON
        },
        success: function(msg){
            // alert("Server Response:\n" + msg);
            if (msg.localeCompare("200")==0) {
                $("#modal-success").modal('show');
            }
        }
    });*/

}
function getCountersArray() {
    var res = [];
    const n = $('table tbody tr').length;
    // alert("n = " + n);
    for (var i = 0; i < n; i++) {
        const counterObj = $("#counter"+i);
        // alert(counterObj.attr('data-device-id'));
        // if (counterObj == null) { alert ("Cont"); continue; }
        const deviceUnqId = $("#row"+i).attr('data-device');
        const counterValue = counterObj.val();
        const companyId = $("#row"+i).attr('data-company');
        const elem = {i: i, companyId: companyId, deviceUnqId: deviceUnqId, counterValue: counterValue};
        // alert("counterValue: " + counterValue + ": " + counterValue.length);
        if (counterValue.length > 0) {
            res.push(elem);
        }
    }
    return res;
}

/* ----------------------------- Validation -------------------------- */
function checkCounterValue(value, lastValue) {
    value = parseFloat(value);
    lastValue = parseFloat(lastValue);
    // alert(lastValue+"!"+value);
    if (isNaN(lastValue)) lastValue = 0;
    // alert(lastValue + " : " + value);
    if (value == null) return 1;
    if (value >= lastValue) return 0;   // correct case
    if (value < lastValue) return 2;
}
function checkCounter(i) {
    const value = $("#counter"+i).val();
    const lastValue = $("#counter_value"+i).text().trim();
    // alert(lastValue + ": " + value);
    const x = checkCounterValue(value, lastValue);
    // const digitsOnly = hasOnlyDigits(value);
    // alert(i + ":" + value + ":" + x);
    // const digitsOnly = hasOnlyDigits(value);
    // if (!digitsOnly) alert(i);
    // alert("Check counter i (" + lastValue + ", " + value + "): " + x);
    return x;
}
function showCounterError(i) {
    // alert("Show error: row " + i);
    $("#counter"+i).addClass("counterError");
    $("#pCounterError").addClass("counterError");
}
function showCounterNoError(i) {
    // alert("Show ok: row " + i);
    $("#counter"+i).removeClass("counterError");
    $("#pCounterError").removeClass("counterError");
}

/* --------------------------------- Show Modals --------------------------------------- */
function showSuccessModal(heading, text) {
    if (text != null) {
        $("#success-text").html(text);
    }
    if (heading != null) {
        $("#success-heading").html(heading);
    }
    $("#modal-success").modal('show');
}
function showFailModal(heading, text) {
    if (text != null) {
        $("#fail-text").html(text);
    }
    if (heading != null) {
        $("#fail-heading").html(heading);
    }
    $("#modal-fail").modal('show');
}

/* --------------------------------- Create Order Page --------------------------------------- */
function checkCreateOrderPageAndSubmit() {
    const deviceValue = $("#device").val();
    const textValue = $("#descr").val();
    let toSubmit = true;
    if (!deviceValue) {
        toSubmit = false;
        $("#device").addClass("errorDevice");
        // alert("Device");
    } else {
        $("#device").removeClass("errorDevice");
    }
    if (!textValue) {
        toSubmit = false;
        $("#descr").addClass("errorDescr");
        // alert("Text");
    } else {
        $("#descr").removeClass("errorDescr");
    }
    if (toSubmit) {
        $("#submitForm").submit();
    }
}

/* --------------------------------- Global Functions --------------------------------------- */
function parseStrToDateTime(str_dt) {
    // return Date.parse(str_dt, "yyyy-MM-dd HH:mm:ss");
    return Date.parse(str_dt);
}
function getCurDateTime() {
    return Date.now();
}