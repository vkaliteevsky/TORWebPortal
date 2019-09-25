<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/check.php');
$seo['title'] = 'Экспорт';
if (($roleId == 1) or ($roleId == 2)) {
	header("Location: /orders/");
}
//$company_devices = getVisibleOrders($userdata["user_id"], False, $roleId); // contains list of visible orders
$companies = getCompanies(False); // contains list of visible orders
//$orders = getVisibleOrders($userdata["user_id"], False, $roleId); // contains list of visible orders
//$counterHistory =getCounterHistory(False);
require_once($_SERVER['DOCUMENT_ROOT'] . '/header.php');
?>
<div  id="export_page">
    <div class="row">
        <div class="col-lg-12">
            <h1 style="font-size: 28px;font-weight: 700;margin-bottom: 15px;">Экспорт</h1>
            <form action="excel.php" method="post">

                <select name="companySelect" class="form-control">
                    <option value="0">Все компании</option>
                    <? foreach ($companies as $k => $v) { ?>
                        <option value="<? echo $companies[$k]['company_id']; ?>"><? echo $companies[$k]['company_short_name']; ?></option>
                    <? }; ?>
                </select>

                <select name="counter_dt" class="form-control">
                    <option value="0"> Все периоды</option>
                    <option value="1">Месяц</option>
                    <option value="2">Квартал</option>
                    <option value="3">Год</option>
                </select>
                <br>
                <br>
                <br>
                <br><br>
                <button class="btn"> Экспорт</button>

            </form>
        </div>
    </div>
</div>
<script>
          $(document).ready(function() {
              $('select').niceSelect();
          });
</script>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/footer.php');
?>
