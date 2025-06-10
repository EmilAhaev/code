<?
$eventManager = Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['CCustomPropAttach', 'GetUserTypeDescription']
);

class CCustomPropAttach
{
    //описываем поведение пользовательского свойства
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE'           => 'S',
            'USER_TYPE'              => 'propAttach',
            'DESCRIPTION'            => 'Привязка к свойствам',
            'GetPropertyFieldHtml'   => array('CCustomPropAttach', 'GetPropertyFieldHtml'),
            'ConvertToDB'            => array('CCustomPropAttach', 'ConvertToDB'),
            'ConvertFromDB'          => array('CCustomPropAttach', 'ConvertFromDB')
        );
    }
    //формируем поля
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        $res = CIBlock::GetProperties(Constants::PRODUCT_IBLOCK_ID, array('SORT' => 'ASC'));
        $PROP_VALUES = $value['VALUE'];
        $PROP_BY_CODES = [];

        if (is_array($PROP_VALUES)) {
            $PROP_VALUES_CODES = array_keys($PROP_VALUES);
        } else {
            $PROP_VALUES_CODES = [];
        }

        $PROP_LIST = [];
        while ($prop = $res->Fetch()) {
            $PROP = [];
            $PROP['NAME'] = $prop['NAME'];
            $PROP['CODE'] = $prop['CODE'];
            $PROP['ID'] = $prop['ID'];
            if (!in_array($prop['CODE'], $PROP_VALUES_CODES)) {
                $PROP_LIST[] = $PROP;
            }
            $PROP_BY_CODES[$prop['CODE']] = $prop;
        }

        ob_start();
        ?>
        <style>
            .scroll_area {
                height: 600px;
                overflow-y: scroll;
                display: inline-block;
                border-bottom: 2px solid #9e9e9e;
                margin-bottom: 20px;
                margin-top: 20px;
            }
            .prop_attach_table {
                border-spacing: 5px;
            }
            .prop_attach_table tr:nth-child(odd) {
                background: #eaeaea;
            }
            .prop_attach_table td {
                padding: 5px;
            }
            .prop_attach_table th {
                padding: 10px;
            }
            .custom-prop-delete {
                position: initial; cursor: pointer
            }
        </style>
        <br />
        <div class="prop_container_js">
            <div>
                <table class="prop_attach_table">
                    <thead>
                    <th>
                        ID свойства
                    </th>
                    <th>
                        Код свойства
                    </th>
                    <th>
                        Сортировка
                    </th>
                    <th></th>
                    </thead>
                    <tbody class="input_container_js">
                    <?foreach ($PROP_VALUES as $code => $sort) {?>
                        <tr class="prop_row_js">
                            <td>
                                <?= $PROP_BY_CODES[$code]['ID']?>
                            </td>
                            <td>
                                <?= $code?>
                            </td>
                            <td>
                                <input type="text" value="<?= $sort?>" name="<?= $strHTMLControlName["VALUE"]?>[<?= $code?>]">
                            </td>
                            <td>
                                <span class="custom-prop-delete js_delete_row bx-core-popup-menu-item-icon adm-menu-delete"></span>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
                <a href="javascript:void(0)" class="show_prop_table_js">Выбрать свойства</a>
                <br />
            </div>

            <div class="scroll_area new_prop_table_js" style="display: none">
                <br />
                <table class="prop_attach_table" >
                    <?foreach ($PROP_LIST as $prop) {?>
                        <tr class="prop_row_js">
                            <td>
                                <?= $prop['ID']?>
                            </td>
                            <td>
                                <?= $prop['NAME']?>
                            </td>
                            <td>
                                <?= $prop['CODE']?>
                            </td>
                            <td>
                                <div style="cursor: pointer" class="btn add_prop_js" data-name="<?= $strHTMLControlName["VALUE"]?>" data-propid="<?= $prop['ID']?>" data-code="<?= $prop['CODE']?>">Добавить</div>
                            </td>
                        </tr>
                    <?}?>
                </table>
            </div>
        </div>
        <script>
            if (!window.initCustomPropHandlers) {
                window.initCustomPropHandlers = true;

                document.addEventListener('click', function (event) {
                    if (event.target.classList.contains('add_prop_js')) {
                        let container = event.target.closest('.prop_container_js').querySelector('.input_container_js');
                        let html = '<tr class="prop_row_js">';
                        html += '<td>'+ event.target.dataset.propid +'</td>';
                        html += '<td>'+ event.target.dataset.code +'</td>';
                        html += '<td>';
                        html += '<input type="text" value="200" name="' + event.target.dataset.name +'['+ event.target.dataset.code +']">';
                        html += '</td>';
                        html += '<td>';
                        html += '<span class="custom-prop-delete js_delete_row bx-core-popup-menu-item-icon adm-menu-delete"></span>';
                        html += '</td>';
                        html += '</tr>';
                        container.innerHTML += html;

                        event.target.closest('.prop_row_js').remove();

                    }
                    if (event.target.classList.contains('js_delete_row')) {
                        event.target.closest('.prop_row_js').remove();
                    }
                    if (event.target.classList.contains('show_prop_table_js')) {
                        let newTable = event.target.closest('.prop_container_js').querySelector('.new_prop_table_js');
                        newTable.style.display = (newTable.style.display == 'none') ? 'block' : 'none';
                    }
                });
            }
        </script>
        <?

        $html = ob_get_contents();
        ob_end_clean();

        return  $html;
    }
    //сохраняем в базу
    public static function ConvertToDB($arProperty, $value)
    {
        if (is_array($value['VALUE'])) {
            asort($value['VALUE']);
        }
        $value['VALUE'] = serialize($value['VALUE']);
        $value['DESCRIPTION'] = '';
        return $value;
    }
    //читаем из базы
    public static function ConvertFromDB($arProperty, $value)
    {
        $value['DESCRIPTION'] = '';
        $arVal = unserialize($value['VALUE']);
        if (is_array($arVal)) {
            $value['VALUE'] = $arVal;
        } else {
            $value['VALUE'] = [];
        }
        return $value;
    }
}
