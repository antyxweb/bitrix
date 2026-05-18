<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var CMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */

$APPLICATION->SetTitle(GetMessage('ADMIN_APPOINTMENTS_LIST_TITLE'));

$columns = $arResult['COLUMNS'];
$rows = $arResult['ROWS'];
$filter = $arResult['FILTER'];
?>
<div class="d-flex">
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.filter',
        '',
        [
            'FILTER_ID' => $arResult['FILTER_ID'],
            'GRID_ID' => $arResult['GRID_ID'],
            'FILTER' => $filter,
            'ENABLE_LIVE_SEARCH' => true,
            'ENABLE_LABEL' => true,
        ]
    );
    ?>
    <div class="admin-category-list-add-btn-block">
        <a href="<?=$arParams['URL_TO_DETAIL']?>" class="ui-btn ui-btn-primary new-applicant-btn">
            <?=GetMessage('ADMIN_APPOINTMENTS_LIST_NEW_APPOINTMENT')?>
        </a>
    </div>
</div>
<?php
$gridSnippet = new \Bitrix\Main\Grid\Panel\Snippet();

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'COLUMNS' => $columns,
        'ROWS' => $rows,
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'SHOW_ROW_CHECKBOXES' => true,
        'NAV_OBJECT' => $arResult['NAV'],
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => $arResult['PAGE_SIZES'],
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => true,
        'SHOW_PAGINATION' => true,
        'SHOW_SELECTED_COUNTER' => false,
        'SHOW_TOTAL_COUNTER' => true,
        'TOTAL_ROWS_COUNT' => $arResult['NAV']->getRecordCount(),
        'SHOW_PAGESIZE' => true,
        'SHOW_ACTION_PANEL' => true,
        'ACTION_PANEL' => [
            'GROUPS' => [
                [
                    'ITEMS' => [
                        $gridSnippet->getEditButton(),
                        $gridSnippet->getRemoveButton(),
                    ]
                ]
            ]
        ],
        'ALLOW_COLUMNS_SORT' => true,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_INLINE_EDIT' => true,
        'ALLOW_SORT' => true,
        'ALLOW_PIN_HEADER' => true,
    ]
);
?>
<script>
    function deleteAppointment(id, slot_id) {
        let questionStatusDelete = '<?=GetMessage('ADMIN_APPOINTMENTS_LIST_QUESTION_DELETE_APPOINTMENT')?>';
        let question = confirm(questionStatusDelete.replace('#APPOINTMENT_ID#', id));
        if (question) {
            BX.ajax.runAction('norbit:appointment.Controller.AppointmentController.delete', {
                data: {
                    id: id,
                    slot_id: slot_id,
                }
            }).then(function (response) {
                console.log(response);
                const grid = BX.Main.gridManager.getInstanceById('<?=$arResult['GRID_ID']?>');
                if (grid) {
                    grid.reloadTable();
                }
            }, function (response) {
                console.log(response);
                alert('<?=GetMessage('ADMIN_APPOINTMENTS_LIST_APPOINTMENT_NOT_DELETED')?>');
            });
        }
    }
</script>
