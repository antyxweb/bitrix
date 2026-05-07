<?
// определяем в какой папке находится модуль, если в bitrix, инклудим файл с меню из папки bitrix
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/norbit.appointment/")) {
    // присоединяем и копируем файл
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/norbit.appointment/admin/appointment_list.php");
}

// определяем в какой папке находится модуль, если в local, инклудим файл с меню из папки local
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/modules/norbit.appointment/")) {
    // присоединяем и копируем файл
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/norbit.appointment/admin/appointment_list.php");
}
