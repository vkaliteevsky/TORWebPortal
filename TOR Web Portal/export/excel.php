<?
require_once($_SERVER['DOCUMENT_ROOT'].'/php/auth/check.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/php/PHPExcel.php');

if ($_POST['companySelect'] == 0) {
	$counterCompanyHistory = getCounterHistory(False);
} else {
	$counterCompanyHistory = getCounterHistoryCompanyName($_POST['companySelect'],False);
}
$counterHistory =getCounterHistory(False);

$phpExcel = new PHPExcel();
$phpExcel->setActiveSheetIndex(0);
//$phpExcel->createSheet();
$active_sheet = $phpExcel->getActiveSheet();
$active_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

$active_sheet->setTitle('Test');

$active_sheet->getHeaderFooter()->setOddHeader("Шапка прайс листа");
$active_sheet->getHeaderFooter()->setOddFooter("Подвал прайс листа");

$active_sheet->getColumnDimension('A')->setWidth(20);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(20);
$active_sheet->getColumnDimension('D')->setWidth(20);
$active_sheet->getColumnDimension('F')->setWidth(20);
$active_sheet->getColumnDimension('G')->setWidth(20);
$active_sheet->getColumnDimension('H')->setWidth(20);
$active_sheet->getColumnDimension('I')->setWidth(20);
$active_sheet->getColumnDimension('E')->setWidth(20);

$active_sheet->setCellValue('A1', 'id компании');
$active_sheet->setCellValue('B1', 'Название компании');
$active_sheet->setCellValue('C1', 'дата счетчика');
$active_sheet->setCellValue('D1', 'id пользователя');
$active_sheet->setCellValue('F1', 'уникальный id');
$active_sheet->setCellValue('G1', 'Модель');
$active_sheet->setCellValue('H1', 'Серийный номер');
$active_sheet->setCellValue('E1', 'Имя пользователя');
$active_sheet->setCellValue('I1', 'Счетчик');
$row_start = 2;
$i = 0;


if($_POST['companySelect']==0){
    $row_start = 2;
    for ($b = 0; $b < count($counterHistory); $b++) {
        if ($_POST['counter_dt'] == 0) {
            $row_next = $row_start + $i;
            $active_sheet->setCellValue('D' . $row_next, $counterHistory[$b]['user_id']);
            $CounterUserId = $counterHistory[$b]['user_id'];
            $UserName = getUserShortName($CounterUserId, False);
            $active_sheet->setCellValue('E' . $row_next, $UserName[0]['user_short_name']);
            $active_sheet->setCellValue('F' . $row_next, $counterHistory[$b]['device_unq_id']);
            $active_sheet->setCellValue('C' . $row_next, $counterHistory[$b]['counter_dt']);
            $active_sheet->setCellValue('A' . $row_next, $counterHistory[$b]['company_id']);
            $CounterCompanyName = $counterHistory[$b]['company_id'];
            $CompanyName = getCompanyShortName($CounterCompanyName, False);
            $active_sheet->setCellValue('B' . $row_next, $CompanyName[0]['company_short_name']);
            $active_sheet->setCellValue('I' . $row_next, $counterHistory[$b]['counter_value']);
            $UnqId = $counterHistory[$b]['device_unq_id'];
            $deviceName = getHistoryDevicesName($UnqId, False);
            $active_sheet->setCellValue('H' . $row_next, $deviceName[0]['device_model']);
            $active_sheet->setCellValue('G' . $row_next, $deviceName[0]['device_serial_number']);
            $i++;
        }
        else{
            $row_next = $row_start + $i;
            $active_sheet->setCellValue('D' . $row_next, $counterHistory[$b]['user_id']);
            $CounterUserId = $counterHistory[$b]['user_id'];
            $UserName = getUserShortName($CounterUserId, False);
            $active_sheet->setCellValue('E' . $row_next, $UserName[0]['user_short_name']);
            $active_sheet->setCellValue('F' . $row_next, $counterHistory[$b]['device_unq_id']);
            $active_sheet->setCellValue('C' . $row_next, $counterHistory[$b]['counter_dt']);
            $active_sheet->setCellValue('A' . $row_next, $counterHistory[$b]['company_id']);
            $CounterCompanyName = $counterHistory[$b]['company_id'];
            $CompanyName = getCompanyShortName($CounterCompanyName, False);
            $active_sheet->setCellValue('B' . $row_next, $CompanyName[0]['company_short_name']);
            $active_sheet->setCellValue('I' . $row_next, $counterHistory[$b]['counter_value']);
            $UnqId = $counterHistory[$b]['device_unq_id'];
            $deviceName = getHistoryDevicesName($UnqId, False);
            $active_sheet->setCellValue('H' . $row_next, $deviceName[0]['device_model']);
            $active_sheet->setCellValue('G' . $row_next, $deviceName[0]['device_serial_number']);
            $i++;
        }
    }

}
else {
    if(count($counterCompanyHistory) > 0){
        $row_start = 2;
    for ($b = 0; $b < count($counterCompanyHistory); $b++) {
        $row_next = $row_start + $i;
        $active_sheet->setCellValue('D' . $row_next, $counterCompanyHistory[$b]['user_id']);
        $CounterUserId = $counterCompanyHistory[$b]['user_id'];
        $UserName = getUserShortName($CounterUserId ,False);
        $active_sheet->setCellValue('E' . $row_next, $UserName[0]['user_short_name']);
        $active_sheet->setCellValue('F' . $row_next, $counterCompanyHistory[$b]['device_unq_id']);
        $active_sheet->setCellValue('C' . $row_next, $counterCompanyHistory[$b]['counter_dt']);
        $active_sheet->setCellValue('A' . $row_next, $counterCompanyHistory[$b]['company_id']);
        $CounterCompanyName = $counterCompanyHistory[$b]['company_id'];
        $CompanyName = getCompanyShortName($CounterCompanyName ,False);
        $active_sheet->setCellValue('B' . $row_next, $CompanyName[0]['company_short_name']);
        $active_sheet->setCellValue('I' . $row_next, $counterCompanyHistory[$b]['counter_value']);
        $UnqId = $counterCompanyHistory[$b]['device_unq_id'];
        $deviceName = getHistoryDevicesName($UnqId ,False);
        $active_sheet->setCellValue('H' . $row_next, $deviceName[0]['device_model']);
        $active_sheet->setCellValue('G' . $row_next, $deviceName[0]['device_serial_number']);
        $i++;
    }}
    else{
        $active_sheet->setCellValue('A2', 'Ничего не найдено');
    }
}

header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Test_File_2.xls");

$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
$objWriter->save('php://output');

exit();

?>