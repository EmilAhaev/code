<?php
//получение значений свойств типа список + полей самого свойства на d7

$enums = \Bitrix\Iblock\PropertyTable::getList([
    'select' => [
        'CODE',
        'ENUM_ID' => 'ENUM.ID',
    ],
    'filter' => [
        'IBLOCK_ID' => $IBLOCK_ID,
        'ACTIVE' => 'Y',
        '=CODE' => 'PROP_CODE',
        'ENUM.VALUE' => 'Need val'
    ],
    'runtime' => [
        'ENUM' => [
            'data_type' => '\Bitrix\Iblock\PropertyEnumerationTable',
            'reference' => [
                'this.ID' => 'ref.PROPERTY_ID',
            ]
        ]
    ],
])->fetchAll();
