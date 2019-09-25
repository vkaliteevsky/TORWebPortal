<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
$seo['title'] = 'Обратная связь';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ((isset($_POST["text_request"]) and (strlen($_POST["text_request"]) > 0))) {
        $text = "<b>Текст сообщения:</b> " .$_POST["text_request"];
        try {
            $rs = sendEmail(getSupportEmail(), "", "Поступление вопроса от ".$userdata["short_name"], $text);
            if (!($rs == True)) $submitStatus = -12;
        } catch (Exception $e) {
            $submitStatus = -12;
        }
    } else {
        $submitStatus = -1;
    }
    if (!isset($submitStatus)) $submitStatus = 1;
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');

?>
<div  id="contact_page">
    <h1 style="font-size: 28px;font-weight: 700;margin-bottom: 15px;">Обратная связь</h1>
	<div class="row">
		<div class="col-lg-12">
			<form action="" method="POST" class="mt-2">
				<textarea name="text_request" id="text_request" cols="30" class="form-control"  rows="10" placeholder="Сообщение"></textarea>
				<input type="submit" id="sub_btn" class="btn" value="Отправить">
			</form>
		</div>
	</div>
</div>
<script>
    <? if (isset($submitStatus)) { ?>
        $(document).ready(function() {
            <? if ($submitStatus >= 1) { ?>
            showSuccessModal("Сообщение успешно отправлено", null);
            <? } else if ($submitStatus <= 0) { ?>
            showFailModal("Возникла ошибка", "Заполните поле для ввода сообщения");
            <? } ?>
        });
    <? } ?>
</script>
<?php
require('../footer.php');
?>