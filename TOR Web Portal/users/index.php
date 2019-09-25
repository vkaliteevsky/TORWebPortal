<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
if ($roleId == 6) {
    header("Location: ../storage/");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['userId'])) {
        $responseCode = -6;
    }
    if (!isset($_POST['password'])) {
        $responseCode = -15;
    }
    if ((isset($_POST['userId'])) && (isset($_POST['password']))) {
        $userId = $_POST['userId'];
        $password = $_POST['password'];
//        echo "TTT:".$password."!".$userId." ";
        $dbOutput = updatePassword($userId, $password);
//        echo "Works";
        if (!$dbOutput) {
            $responseCode = 0;
        }
    }
    if (!isset($responseCode)) {
        $responseCode = 1;
    }
}
$seo['title'] = 'Список пользователей';

$roleId = $userdata['role_id'];
if ($roleId != 5) {
    header("Location: ../orders/");
}

$userId = $userdata['user_id'];
$users = getAllActiveUsers();

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
<div class="workField">
    <div class="headingRow">
            <h1 class="heading">Пользователи</h1>
            <div class="searchBlock">
                <img src="../img/icons/search-disabled.png">
                <input type="text" id="search" placeholder="Поиск" oninput="filter();">
            </div>
    </div>
    <div class="clear"></div>
    <div class="t_tb">
        <div class="row t_heading">
            <div class="col-md-3 col-3 padding-0">
                <p>КРАТКОЕ ИМЯ <img src="../img/icons/chevron-down.png"></p>
            </div>
            <div class="col-md-2 col-3 padding-0">
                <p>ЛОГИН <img src="../img/icons/chevron-down.png"></p>
            </div>
            <div class="col-md-3 col-3 padding-0">
                <p>КОМПАНИЯ <img src="../img/icons/chevron-down.png"></p>
            </div>
            <div class="col-md-2 col-3 padding-0">
                <p>РОЛЬ <img src="../img/icons/chevron-down.png"></p>
            </div>
            <div class="col-md-2 padding-0 d-none d-md-block">
                <p>ПОЧТА <img src="../img/icons/chevron-down.png"></p>
            </div>
        </div>
        <? for ($i = 0; $i < count($users); $i++) { ?>
        <? $curUser = $users[$i]; ?>
            <a href="/user/index.php?user_id=<? echo $curUser['user_id']; ?>">
                <div class="row t_row" id="row_<? echo $i; ?>"
                    data-filter="<? echo "{$curUser['user_long_name']};{$curUser['login']};{$curUser['company_short_name']};{$curUser['role_name']};{$curUser['user_email']}"; ?>"
                >
                    <div class="col-md-3 col-3 padding-0">
                        <p id="userShortName_<? echo $i; ?>"><? echo $curUser['user_short_name']; ?></p>
                    </div>
                    <div class="col-md-2 col-3 padding-0">
                        <p id="login_<? echo $i; ?>"><? echo $curUser['user_login']; ?></p>
                    </div>
                    <div class="col-md-3 col-3 padding-0">
                        <p id="companyShortName_<? echo $i; ?>"><? echo $curUser['company_short_name']; ?></p>
                    </div>
                    <div class="col-md-2 col-3 padding-0">
                        <p id="role_<? echo $i; ?>"><? echo $curUser['role_name']; ?></p>
                    </div>
                    <div class="col-2 padding-0 d-none d-md-block">
                        <p id="email_<? echo $i; ?>"><? echo $curUser['user_email']; ?></p>
                    </div>
                </div>
            </a>
        <? } ?>
    </div>
    <div class="row" style="margin-top: 40px;">
        <div class="col-12 text-center">
            <a href="/add-user/"><button type="button" class="orderSend">Добавить</button></a>
        </div>
    </div>
</div>
<script>
    function filter() {
        const n = <? echo count($users); ?>;
        const searchText = document.getElementById("search").value.toUpperCase();
        for (let i = 0; i < n; i++) {
            const docText = $('#row_'+i).attr('data-filter').toUpperCase();
            const isContain = docText.includes(searchText);
            //alert(docText + " " + searchText + " " + isContain);
            if (isContain) {
                $('#row_'+i).css('display', '');
            } else {
                $('#row_'+i).css('display', 'none');
            }
        }
    }
</script>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
?>
