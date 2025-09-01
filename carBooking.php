<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle('Бронирование автомобилей');
$APPLICATION->IncludeComponent("testWork:car.booking", "", [
    'DATETIME_START' => $_GET['dateTimeStart'],
    'DATETIME_END' => $_GET['dateTimeEnd'],
],
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>