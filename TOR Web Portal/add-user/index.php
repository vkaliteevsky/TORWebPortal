<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($userdata['role_id'] != 5) {
        header("Location: ../orders/");
        return;
    }
    $userLongName =  $_POST['user_long_name'];
    $userShortName =  $_POST['user_short_name'];
    $userLogin =  $_POST['user_login'];
    $userPassword = $_POST['user_password'];
    $companyId = $_POST['company'];
    $roleId = $_POST['role'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone'];
    if (!isset($userLongName)) {
        $statusCode = -19;
    } else if (!isset($userShortName)) {
        $statusCode = -18;
    } else if (!isset($userLogin)) {
        $statusCode = -20;
    } else if (!isset($companyId)) {
        $statusCode = -17;
    } else if (!isset($roleId)) {
        $statusCode = -16;
    } else if (!isset($userPassword)) {
        $statusCode = -22;
    } else {
        $logins = executeSelectRequest("select user_login from users where (user_login = '{$userLogin}')");
        if (count($logins) > 0) {
            $statusCode = -21;
        } else {
            $dbOutput = insertUser($userId, $userLogin, $userPassword, $userShortName, $userLongName, $roleId, $companyId, $email, $phone);
            if (!$dbOutput) {
                $statusCode = 0;
            } else {
                $statusCode = 1;
            }
        }
    }
    if (!isset($statusCode)) {
        $statusCode = 1;
    }
//    echo $toDeleteUser.":".$userId.":".$userLongName.":".$userShortName.":".$userLogin.":".$userPassword.":".$companyId.":".$roleId.":".$email.":".$phone."\n";
}
$seo['title'] = 'Добавление пользователя';

$curUser = $userdata['user_id'];
$userId = $_GET['user_id'];

$companies = getCompanies(false);
$roles = getRoles();
if ($userdata['role_id'] != 5) {
    header("Location: ../orders/");
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
<style>
    .userInputs p {
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .workField input {
        border-bottom: 2px solid #20ad65;
        width: 250px;
    }
    .colItem {
        margin: 0px 40px;
    }
    .workField select {
        border-bottom: 2px solid #20ad65 !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-radius: 0px !important;
        padding-left: 0px !important;
        max-width: 250px;
    }
</style>
<div class="workField">
    <div class="row">
        <div class="col-12">
            <h1 class="heading text-center text-sm-left">Добавление пользователя</h1>
        </div>
    </div>
    <form action="" class="text-left userInputs" id="userForm" method="post">
        <div class="row">
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Полное имя</p>
                    <input type="text" placeholder="Иванов Петр Дмитриевич"
                           name="user_long_name" id="user_long_name"
                           value=""
                    >
                    <div id="longNameErr" class="errorTextStyle"></div>
                </div>
            </div>
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Краткое имя</p>
                    <input type="text" placeholder="Иванов П.Д."
                           name="user_short_name" id="user_short_name"
                           value=""
                    >
                    <div id="shortNameErr" class="errorTextStyle"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Логин</p>
                    <input type="text" placeholder="ivanovpd"
                           name="user_login" id="user_login" autocomplete="off"
                           value=""
                    >
                    <div id="loginErr" class="errorTextStyle"></div>
                </div>
            </div>
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Пароль</p>
                    <input type="password" placeholder="******" name="user_password" id="user_password" autocomplete="off">
                    <div id="passwordErr" class="errorTextStyle"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Компания</p>
                    <select placeholder="Балтийский завод" name="company" id="company" class="form-control">
                        <? for ($i = 0; $i < count($companies); $i++) { ?>
                            <option value="<? echo $companies[$i]['company_id']; ?>">
                                <? echo $companies[$i]['company_short_name']; ?>
                            </option>
                        <? } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Роль</p>
                    <select placeholder="Инженер" name="role" id="role" class="form-control">
                        <? for ($i = 0; $i < count($roles); $i++) { ?>
                            <option value="<? echo $roles[$i]['role_id']; ?>">
                                <? echo $roles[$i]['role_name']; ?>
                            </option>
                        <? } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Email</p>
                    <input type="email" placeholder="ivanov@mail.ru" name="user_email" id="user_email" value="">
                    <div id="emailErr" class="errorTextStyle"></div>
                </div>
            </div>
            <div class="col-sm-6 col-12 mt-4">
                <div class="colItem">
                    <p>Телефон</p>
                    <input type="text" placeholder="+7123456789" name="user_phone" id="user_phone" value="">
                    <div id="phoneErr" class="errorTextStyle"></div>
                </div>
            </div>
        </div>
    </form>
    <div class="row" style="margin-top: 40px;">
        <div class="col-12 text-center">
            <button type="button" class="orderSend" onclick="checkSubmit();">Сохранить</button>
        </div>
    </div>
</div>
<script>
    function isEmpty(v) {
        if (v.length >= 1) return false;
        else return true;
    }
    function checkShortName() {
        const name = $('#user_short_name').val();
        let isOk = true;
        if (isEmpty(name)) {
            $('#shortNameErr').html("Заполните краткое имя");
            isOk = false;
        } else {
            $('#shortNameErr').html("");
        }
        return isOk;
    }
    function checkLongName() {
        //alert("Check long name");
        const name = $('#user_long_name').val();
        let isOk = true;
        if (isEmpty(name)) {
            $('#longNameErr').html("Заполните полное имя");
            isOk = false;
        } else {
            $('#longNameErr').html("");
        }
        return isOk;
    }
    function checkLogin() {
        const login = $('#user_login').val();
        // alert("Login: " + login);
        let isOk = true;
        if (login.length < 6) {
            $('#loginErr').html("Логин должен содержать не менее 6 символов");
            isOk = false;
        } else {
            $('#loginErr').html("");
        }
        return isOk;
    }
    function checkPassword() {
        const password = $('#user_password').val();
        let isOk = true;
        if ((password.length < 6)) {
            $("#passwordErr").html("Пароль должен содержать не менее 6 символов");
            isOk = false;
        } else {
            $("#passwordErr").html("");
        }
        return isOk;
    }
    function checkEmail() {
        const email = $('#user_email').val();
        let isOk = true;
        if ((email.length > 0) && (!email.includes('@'))) {
            $('#emailErr').html("Введите корректный Email");
            isOk = false;
        } else {
            $('#emailErr').html("");
        }
        return isOk;
    }
    function checkPhone() {
        return true;
        const phone = $('#user_phone').val();
        let isOk = true;
        if ((phone.length > 0)) {
            $('#phoneErr').html("Введите корректный номер телефона");
            isOk = false;
        } else {
            $('#phoneErr').html("");
        }
        return isOk;
    }
    function checkForm() {
        // alert("Checking form");
        const isShortNameOk = checkShortName();
        const isLongNameOk = checkLongName();
        const isLoginOk = checkLogin();
        const isPasswordOk = checkPassword();
        const isEmailOk = checkEmail();
        const isPhoneOk = checkPhone();
        return isShortNameOk && isLongNameOk && isLoginOk && isPasswordOk && isEmailOk && isPhoneOk;
    }
    function checkSubmit() {
        const isOk = checkForm();
        if (isOk) $("#userForm").submit();
    }

    function deleteUser() {
        $('#toDeleteUser').val("1");
        $('#userForm').submit();
    }
</script>
<script>
    <? if(isset($statusCode)) { ?>
    $(document).ready(function () {
        <? if ($statusCode >= 1) { ?>
        showSuccessModal("Информация успешно обновлена", null);
        <? } else if ($statusCode <= 0) { ?>
        <? $arrText = getErrorHeadingAndText($statusCode); ?>
        showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
        <? } ?>
    });
    <? } ?>
</script>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
?>
