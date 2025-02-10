<? 
    /*
     * Получаем уникальные корневые разделы элементов
     */
    
    \Bitrix\Main\Loader::includeModule('iblock');
    
    // в настройках иб должен быть указан api код инфоблока
    $productClass = \Bitrix\Iblock\Iblock::wakeUp(Constant::PRODUCTS_IBLOCK_ID)->getEntityDataClass();

    $arResult['ROOT_SECTIONS'] = $productClass::getList([
        'select' => [
            'ROOT_SECTION_ID',
            //'ROOT_SECTION_NAME',
            //'ID',
            //'IBLOCK_ID',
            //'SECTIONS_ID' => 'SECTIONS.ID',
            //'SECTIONS_NAME' => 'SECTIONS.NAME',
            //'ROOT_SECTION_ID' => 'ROOT_SECTION.ID',
            'ROOT_SECTION_NAME' => 'ROOT_SECTION.NAME',

        ],
        'filter' => [
            '=ID' => $arParams['FAVORITE_PRODUCTS'],
            '=ROOT_SECTION.DEPTH_LEVEL' => 1
        ],
        'order' => ['ROOT_SECTION.SORT' => 'ASC'],
        'runtime' => [
            'ROOT_SECTION' => [
                'data_type' => '\Bitrix\Iblock\SectionTable',
                'reference' => [
                    'this.IBLOCK_ID' => 'ref.IBLOCK_ID',
                    '>this.SECTIONS.LEFT_MARGIN' => 'ref.LEFT_MARGIN',
                    '<this.SECTIONS.RIGHT_MARGIN' => 'ref.RIGHT_MARGIN',
                ]
            ],
            new \Bitrix\Main\ORM\Fields\ExpressionField('ROOT_SECTION_ID', 'DISTINCT %s ', ['ROOT_SECTION.ID']),
        ],

    ])->fetchAll();
