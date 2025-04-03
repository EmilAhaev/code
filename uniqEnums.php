<?php
/*
 * Получим уникальный ID вариантов списка по их строковому значению
 */
$arIds = \Bitrix\Iblock\PropertyEnumerationTable::getList([
    'select' => [
        'JOIN_ENUM_VAL' => 'JOIN_ENUM.VALUE',
        'MIN_ID'
    ],
    'filter' => [
        'PROPERTY_ID' => $this->propId,
        'VALUE' => $arValues
    ],
    'runtime' => [
        'JOIN_ENUM' => [
            'data_type' => '\Bitrix\Iblock\PropertyEnumerationTable',
            'reference' => [
                'this.VALUE' => 'ref.VALUE',
                'this.PROPERTY_ID' => 'ref.PROPERTY_ID',
            ],
            'join_type' => 'inner'
        ],
        new \Bitrix\Main\ORM\Fields\ExpressionField('MIN_ID', 'MIN(%s) ', ['JOIN_ENUM.ID']),
    ],
    
])->fetchAll();
