<html>
<head>
    <title><? echo $seo['title']; ?></title>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" type="image/x-icon" href="../img/tor-logo.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!--    scripts -->
    <script src="/js/libs/jquery.min.js"></script>
    <script src="/js/libs/popper.min.js"></script>
    <script src="/js/libs/list.min.js"></script>
    <script src="/js/verification.js"></script>
    <script src="/js/calendar.js"></script>
    <script src="/js/jquery.maskedinput.min.js"></script>
    <script src="/js/dist/jquery.inputmask.js"></script>
    <script src="/js/libs/bootstrap.js"></script>
    <script src="/js/calend/js/datepicker.js"></script>
    <link href="/js/calend/css/datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="/js/select/jquery.nice-select.js" defer></script>
<!--    <script src="/js/QRScanner/qr-scanner.min.js"></script>-->
<!--    <script type="text/javascript" src="/js/QRScanner/instascan.min.js"></script>-->
<!--    <script src=“https://rawgit.com/schmich/instascan-builds/master/instascan.min.js”></script>-->

<!--style-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
    <link rel="stylesheet" href="/js/libs/bootstrap.css">
    <link rel="stylesheet" href="/js/libs/bootstrap.reboot.css">
    <link rel="stylesheet" href="/js/libs/bootstrap.grid.css">
    <link rel="stylesheet" href="/css/lk_style.css">
    <link rel="stylesheet" href="/css/k_styles.css?id=<?php echo rand(1, 10000000); ?>">
    <link rel="stylesheet" href="/css/k_styles_mobile.css?id=<?php echo rand(1, 10000000); ?>">
    <? if (!strcmp($_SERVER['PHP_SELF'], "/order/index.php")) { ?>
        <link rel="stylesheet" href="/css/chat.css">
    <? } ?>

    <!-- dropzone -->
    <script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
    <link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">
</head>
<body>
<script>
    function init(){
        var obj = JSON.parse(arr);
        var i = 0;
        for (var key in obj){
            console.log(key);
            document.getElementById('goods-out').innerHTML +='<div class=" row dev__line" id="device_'+i+'" data-device="'+obj[key].device_unq_id+'"><div class="cart col-lg-6"><img src='+obj[key].device_img_src+'"../../../../Desktop" alt=""><div class="name_item"><p class="name">'+obj[key].device_short_name+'</p></div></div><div class="cart1  col-lg-6"><a href="../orders/create-order.php"><button class="oformit">Оформить заявку</button></a><input id="sch_' + i + '" class="schetchik" type="text" placeholder="Счетчик"><button onclick="sendCounter(this);" id="mbtn_' + i + '" class="add-to-cart">Отправить показания</button></div></div>';
            i++;
        }
    }
    function init2(input_arr){
        //alert("Init2: " + input_arr);
        document.getElementById('device').innerHTML = "";
        var obj = JSON.parse(input_arr);
        for (var key in obj){
            document.getElementById('device').innerHTML +='<option data-counter=\''+obj[key].counter_value+'\' value="' + obj[key].device_unq_id + '">'+obj[key].device_model+' (' + obj[key].device_serial_number + ')' + '</option>';
        }}
    function init3(){
        var obj2 = JSON.parse(arr2);
        for (var key in obj2){
            document.getElementById('history').innerHTML +='<tr><td class="nomer"><p>'+obj2[key].order_id+'<p></td><td class="ystroistvo"><p>'+obj2[key].device_short_name+'<p></td><td class="status"><p>'+obj2[key].status_name+'<p></td><td class="prioritet"><p>'+obj2[key].priority_name+'<p></td><td class="comment"><p>'+obj2[key].order_text_request+'<p></td></tr>';
        }
    }
    function setPriors() {
        var obj3 = JSON.parse(priors);
        for (var key in obj3){
            document.getElementById('priority').innerHTML +='<option value="' + obj3[key].priority_id + '">'+obj3[key].priority_name+'</option>';
        }
    }
</script>
<div id="modal-success" class="modal fade">
    <div class="modal-dialog modal-confirm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h5 align="left" id="success-heading">Сообщение успешно отправлено!</h5>
                <p id="success-text" align="left"></p>
                <button  type="button" class="btn btn-success " data-dismiss="modal"><span>Продолжить</span></button>
            </div>
        </div>
    </div>
</div>
<div id="modal-fail" class="modal fade">
    <div class="modal-dialog modal-confirm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h5 align="left" id="fail-heading">Ошибка при отправке сообщения</h5>
                <p id="fail-text"></p>
                <button type="button" class="btn btn-success " data-dismiss="modal"><span>Продолжить</span></button>
            </div>
        </div>
    </div>
</div>
<div class="row" id="head__row">
    <div class="col-xl-1 col-lg-0 col-md-0 col-sm-1"></div>
    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-1 text-right burger-menu__nav" style="text-align: right;"></div>
    <div class="col-xl-6 col-lg-5 col-md-5 col-sm-2  topMenuBtnMobileOff">
        <div class="">
            <a href="../../orders/" class="head__logo"><img src="../img/icons/tor-logo.png"></a>
            <? if (($roleId >= 1) && ($roleId <= 5)) { ?>
                <a href="/create-order/" class="menu__btn_top menu__btn_top_orders">Оформить заявку</a>
            <? } ?>
            <? if (($roleId >= 1) && ($roleId <= 5)) { ?>
                <a href="/device/" class=" menu__btn_top">Оборудование</a>
            <? } ?>
            <?  if (($roleId >= 1)) { ?>
                <a href="/storage" class="sklad menu__btn_top">Склад</a>
            <? } ?>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-3 col-sm-2 text-right">
        <div id="SettingBlock" style="margin-top: -15px;">
            <input class="btn btn-header  name_btn" type="button" style="margin-bottom: -15px; margin-right: -15px;"
                   value="<? echo $userdata["user_short_name"]; ?>">
            <br>
            <a href="/settings/" class="opt_btn">Настройки</a>
            <a href="/php/auth/logout.php?user_id=<? echo $userdata['user_id']; ?>" class="btn btn-header log_btn">Выход</a>

        </div>

    </div>
</div>

<div class="row burger-menu" >
    <a href="../../orders/" class="head__logo_mob"><img src="../img/icons/tor-logo.png"></a>
    <a href="#" class="burger-menu__button">
        <span class="burger-menu__lines"><button> <img
                        src="https://img.icons8.com/ios/50/000000/menu-filled.png"></button></span>
    </a>
    <div class="col-xl-1 col-lg-1 col-md-0 col-sm-0"></div>
    <div class=" col-xl-2 col-lg-2 col-md-3 col-sm-12 text-right burger-menu__nav">

        <div id="main__menu">

            <ul>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?>
                <a href="/create-order/" class="menu__btn_top menu__btn_top_orders displayMobile">Оформить заявку</a>
                <? } ?>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?><a href="/orders/" class="menu__btn">Список заявок</a><? } ?>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?> <a href="/counter/" class=" menu__btn">Подать счетчик </a> <? } ?>
                <? if (($roleId == 1)) { ?> <a href="/contact/" class=" menu__btn">Обратная связь</a> <? } ?>
                <? if (($roleId >=3) && ($roleId <= 5)) { ?> <a href="/export/" class=" menu__btn">Экспорт</a> <? } ?>
                <? if ($roleId == 5) { ?> <a href="/users/" class=" menu__btn">Пользователи</a> <? } ?>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?> <a href="/device/" style="margin-left: 0px;" class=" menu__btn_top displayMobile">Оборудование</a> <? } ?>
                <? if ($roleId >= 1) { ?><a href="/storage/" class="skladMob menu__btn_top displayMobile">Склад</a><? } ?>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?><a href="/settings/" class="skladMob menu__btn_top displayMobile">Настройки</a><? } ?>
                <? if (($roleId >= 1) && ($roleId <= 5)) { ?><a href="/php/auth/logout.php?user_id=<? echo $userdata['user_id']; ?>" class="skladMob menu__btn_top displayMobile">Выход</a><? } ?>
            </ul>

        </div>
    </div>



    <div class=" col-xl-8 col-lg-8 col-md-8 col-sm-12 " class="w100">
        <div class="mobRow">
        </div>
