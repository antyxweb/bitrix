<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("norbit.appointment");

// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$arParams = array(
    'URL_LIST_TO_ALL' => '/bitrix/admin/appointment_list.php',
    'URL_TO_DETAIL' => '/bitrix/admin/appointment_edit.php'
);
?><?php
$APPLICATION->IncludeComponent('norbit:admin.appointments.list', '', $arParams);
?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");