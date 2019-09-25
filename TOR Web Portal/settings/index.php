<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
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
$seo['title'] = 'Смена пароля';

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
<style>
    .errorPassword {
        border: 1px solid red;
    }
    .errorTextStyle {
        color: red;
        font-size: 0.85em;
    }
</style>
<div id="export_page">
    <div class="row">
        <div class="col-12">
            <h3>Смена пароля</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-9 col-12">
            <form action="" method="post" id="passwordForm">
                <input type="hidden" name="userId" id="userId" value="<? echo $userdata['user_id']; ?>">
                <div class="form-group">
                    <span>Введите новый пароль:</span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i> </span>
                        </div>
                        <input id="password" name="password" class="form-control" placeholder="******" type="password">
                    </div>
                    <span id="errText1" class="errorTextStyle"></span>
                </div>
                <div class="form-group">
                    <span>Повторите введенный пароль:</span>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i> </span>
                        </div>
                        <input id="passwordSecond" class="form-control" placeholder="******" type="password">
                    </div>
                    <span id="errText2" class="errorTextStyle"></span>
                </div>
            </form>
            <div class="form-group">
                <div class="input-group">
                    <button class="btn btn-block btn_green" id="sendBtn" style="width: 140px;">Сменить пароль</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#sendBtn').click(function() {
            const password = $('#password').val();
            const passwordSecond = $('#passwordSecond').val();

            const isLengthOk = (password.length >= 5);
            if (!isLengthOk) {
                $('#password').addClass('errorPassword');
                $('#errText1').html("Пароль должен содержать не менее 6 символов");
            } else {
                $('#password').removeClass('errorPassword');
                $('#errText1').html("");
            }

            const arePasswordsEqual = !password.localeCompare(passwordSecond);
            if (!arePasswordsEqual) {
                $('#passwordSecond').addClass('errorPassword');
                $('#errText2').html("Введенные пароли не совпадают");
            } else {
                $('#passwordSecond').removeClass('errorPassword');
                $('#errText2').html("");
            }

            if (isLengthOk && arePasswordsEqual) {
                // alert("Sending Ok");
                $("#passwordForm").submit();
            }
        });
        <? if (isset($responseCode)) { ?>
        <? if ($responseCode >= 1) { ?>
        showSuccessModal("Пароль успешно обновлен", null);
        <? } else if ($responseCode <= 0) { ?>
        <? $arrText = getErrorHeadingAndText($responseCode); ?>
        showFailModal("<? echo $arrText[0]; ?>", "<? echo $arrText[1]; ?>");
        <? } ?>
        <? } ?>
    });
</script>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
?>
