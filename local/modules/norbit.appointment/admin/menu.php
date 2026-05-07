<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;

// подключение ланговых файлов
Loc::loadMessages(__FILE__);

// сформируем верхний пункт меню
$aMenu = array(
    // пункт меню в разделе Контент
    'parent_menu' => 'global_menu_services',
    // сортировка
    'sort' => 1,
    // название пункта меню
    'text' => "SPA-виджет",
    // идентификатор ветви
    "items_id" => "menu_webforms",
    // иконка
    "icon" => "iblock_menu_icon_settings",
);

// дочерния ветка меню
$aMenu["items"][] =  array(
    // название подпункта меню
    'text' => 'Список записей',
    // ссылка для перехода
    'url' => 'appointment_list.php?lang=' . LANGUAGE_ID
);

// возвращаем основной массив $aMenu
return $aMenu;
