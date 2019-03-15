<?php
use Bitrix\Main\Loader,
    Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale;
    
/**
* добавляет поле время до и после и применяет скидку относительно времени суток
*/       
    
Loader::includeModule('sale');
AddEventHandler("sale", "OnCondSaleControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));//корзина
AddEventHandler("catalog", "OnCondCatControlBuildList", Array("CatalogCondCtrlStoreQuantity", "GetControlDescr"));//каталог
class CatalogCondCtrlStoreQuantity extends CSaleCondCtrlCommon
{
    public static function GetClassName()
    {
        return __CLASS__;
    }
    
    public static function GetControlID()
    {
        return array('TimeWork');
    }
    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = array(
            'controlgroup' => true,
            'group' =>  false,
            'label' => 'Кастомные',
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => array()
        );
        foreach ($arControls as &$arOneControl)
        {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => array(
                    array(
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $arOneControl['PREFIX']
                    ),
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                )
            );
        }
        
        
        if (isset($arOneControl))
            unset($arOneControl);
        return $arResult;
    }
    
    public static function GetControls($strControlID = false)
    {
                
        $arControlList = array(
            'TimeWork' => array(
                'ID' => 'TimeWork',
                'FIELD' => 'TIME_WORK',
                'FIELD_TYPE' => 'string',
                'LABEL' => 'Время действия',
                'PREFIX' => 'Время действия',
                'LOGIC' => static::GetLogic(array(
                        BT_COND_LOGIC_GR,
                        BT_COND_LOGIC_LS,
                        )),
                'JS_VALUE' => array(
                    'type' => 'input'
                ),
                'PHP_VALUE' => ''
            ),
        );
        foreach ($arControlList as &$control)
        {
            if (!isset($control['PARENT']))
                $control['PARENT'] = true;
            $control['MULTIPLE'] = 'N';
        }
        
        unset($control);
        if ($strControlID === false)
        {
            return $arControlList;
        }
        elseif (isset($arControlList[$strControlID]))
        {
            return $arControlList[$strControlID];
        }
        else
        {
            return false;
        }
    }
    
    public static function Generate($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        
        $strResult = '';
        $resultValues = array();
        $arValues = false;
        
        $time = $arOneCondition['value'];
        $logic =  $arOneCondition['logic'];
        
        $strResult  = '(CatalogCondCtrlStoreQuantity::qStoreC("'.$time.'", "'. $logic.'"))==true';
        
        return  $strResult;        
        
    }
        
    // функция условие по которому проверяется, нужно ли применять скидку на корзину.
    //возратить надо true или false
    
    public static function qStoreC($time, $logic)
    {
        $result = false;
        if (!empty($time)) {
            $timestampTime = strtotime($time);
        
            if ($logic == 'Great') { // больше
                if (time() >= $timestampTime) {
                    $result = true;    
                }   
            } elseif ($logic == 'Less') {
                if (time() < $timestampTime) {
                    $result = true;     
                }   
            }     
            
                
        }
        
        return $result;
    }
}
?>
