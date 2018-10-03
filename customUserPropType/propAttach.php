<?
/**
  Запихиваем это в init.php  и получаем пользовательское свойство у элемента - привязка к свойству.
*/

AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CCustomPropAttach', 'GetUserTypeDescription')); 
class CCustomPropAttach 
{
    //описываем поведение пользовательского свойства
    function GetUserTypeDescription() 
    {
       return array(
         'PROPERTY_TYPE'           => 'S',
         'USER_TYPE'              => 'propAttach',
         'DESCRIPTION'            => 'Привязка к свойству',
         'GetPropertyFieldHtml'   => array('CCustomPropAttach', 'GetPropertyFieldHtml'),
         'ConvertToDB'            => array('CCustomPropAttach', 'ConvertToDB'),
         'ConvertFromDB'          => array('CCustomPropAttach', 'ConvertFromDB')
       );
    }
    //формируем поля
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) 
    {
        $value['VALUE'] = unserialize($value['VALUE']);
        
        $res = CIBlock::GetProperties($arProperty['IBLOCK_ID'], array('SORT' => 'ASC'));
        $PROP_VALUES = $value['VALUE'];
        
        $PROP_LIST = [];
        while ($prop = $res->Fetch()) {
            $PROP = [];    
            $PROP['NAME'] = $prop['NAME'];
            $PROP['CODE'] = $prop['CODE'];
            if(!empty($PROP_VALUES[$prop['CODE']]) && $PROP_VALUES[$prop['CODE']] == '1') {
                $PROP['IS_SELECTED'] = true;    
            } else {
                 $PROP['IS_SELECTED'] = false;    
            }    
            $PROP_LIST[] = $PROP;    
        }

        ob_start();
        ?>
            <style>
                #prop_attach_container {
                    height: 250px;
                    overflow-y: scroll;
                    display: inline-block;    
                }
                #prop_attach_table {
                    border-spacing: 5px;    
                }
                #prop_attach_table tr:nth-child(odd) {
                    background: #eaeaea;
                }
                #prop_attach_table td {
                    padding: 5px;
                }   
            </style>
            <div id="prop_attach_container">
                <table id="prop_attach_table" style="">
                <?foreach ($PROP_LIST as $prop) {?>
                    <tr style="">
                        <td>
                            <?= $prop['NAME']?>
                        </td>
                        <td>
                            <input 
                              type="checkbox" 
                              name="<?= $strHTMLControlName["VALUE"]?>[<?= $prop['CODE']?>]" 
                              value="1"/
                              <?if ($prop['IS_SELECTED']) {?>
                                checked
                              <?}?>
                            >
                        </td>
                    </tr>
                <?}?>
                </table>
            </div>
        <?
       
        $html = ob_get_contents();
        ob_end_clean();
       
        return  $html;
    }
    //сохраняем в базу
    function ConvertToDB($arProperty, $value)
    {  
        $value['VALUE'] = serialize($value['VALUE']);  
        return $value;
    }
    //читаем из базы
    function ConvertFromDB($arProperty, $value)
    {         
        $value['DESCRIPTION'] = '';
        return $value;
    }
}
