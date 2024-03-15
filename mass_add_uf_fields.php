<?php
global $APPLICATION;
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

    $BASE_FIELD = array (
        'ENTITY_ID' => 'USER',
        'FIELD_NAME' => 'UF_BASE_CODE',
        'USER_TYPE_ID' => 'string',
        'XML_ID' => '',
        'SORT' => '100',
        'MULTIPLE' => NULL,
        'MANDATORY' => NULL,
        'SHOW_FILTER' => 'N',
        'SHOW_IN_LIST' => NULL,
        'EDIT_IN_LIST' => NULL,
        'IS_SEARCHABLE' => NULL,
        'SETTINGS' =>
            array (
                'DEFAULT_VALUE' => '',
                'SIZE' => '80',
                'ROWS' => '1',
                'MIN_LENGTH' => '0',
                'MAX_LENGTH' => '0',
                'REGEXP' => '',
            ),
        'EDIT_FORM_LABEL' =>
            array (
                'ru' => 'Базовое имя',
                'en' => '',
                'la' => '',
                'ua' => '',
            ),
        'LIST_COLUMN_LABEL' =>
            array (
                'ru' => '',
                'en' => '',
                'la' => '',
                'ua' => '',
            ),
        'LIST_FILTER_LABEL' =>
            array (
                'ru' => '',
                'en' => '',
                'la' => '',
                'ua' => '',
            ),
        'ERROR_MESSAGE' =>
            array (
                'ru' => '',
                'en' => '',
                'la' => '',
                'ua' => '',
            ),
        'HELP_MESSAGE' =>
            array (
                'ru' => '',
                'en' => '',
                'la' => '',
                'ua' => '',
            ),
    );

    $arFieldsToAdd = [
        ['CODE' => 'UF_FULL_NAME', 'NAME' => 'Полное наименование организации'],
        ['CODE' => 'UF_RS', 'NAME' => 'Р/с'],
        ['CODE' => 'UF_BIK', 'NAME' => 'БИК'],
        ['CODE' => 'UF_KPP', 'NAME' => 'КПП'],
        ['CODE' => 'UF_BANK', 'NAME' => 'Банк'],
        ['CODE' => 'UF_KS', 'NAME' => 'К/с'],
        ['CODE' => 'UF_OGRN', 'NAME' => 'ОГРН'],
        ['CODE' => 'UF_YR_ADDRESS', 'NAME' => 'Юридический адрес'],
        ['CODE' => 'UF_ACT_ADDRESS', 'NAME' => 'Фактический адрес'],
    ];

    $obUserField  = new CUserTypeEntity;

    foreach ($arFieldsToAdd as $field) {
        $add_fields = $BASE_FIELD;
        $add_fields['FIELD_NAME'] = $field['CODE'];
        $add_fields['EDIT_FORM_LABEL']['ru'] = $field['NAME'];

        $ID = $obUserField->Add($add_fields);
        if (!$ID) {
            if($e = $APPLICATION->GetException())
            {
                $message = new CAdminMessage(GetMessage("USER_TYPE_SAVE_ERROR"), $e);
                echo "<pre>";
                var_export($e->GetString());
                echo "</pre>";
            }
        } else {
            echo "<pre>"; var_export($field['CODE'] . ' - ' . 'ok!'); echo "</pre>";
        }
    }
}
