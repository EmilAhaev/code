<?php
use Bitrix\Main\Loader,
    Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale;
    
/**
* С помощью этого когда, можно бобавить свое условие в выполнение скидки на корзину. Выделить отдельный товар тут не получится, скидка будет применяться на всю корзину. 
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
        return array('CountProduct');
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
            'CountProduct' => array(
                'ID' => 'CountProduct',
                'FIELD' => 'ITEM_POSITION_IN_BASKET',
                'FIELD_TYPE' => 'int',
                'LABEL' => 'Какой по счету товар в корзине',
                'PREFIX' => 'Какой по счету товар в корзине',
                'LOGIC' => static::GetLogic(array(BT_COND_LOGIC_EQ)),
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
        if($arOneCondition['logic']=='Equal')
        {
            $logic='true';
        }
        else
        {
            $logic='false';
        }
        
        $productCnt = $arOneCondition['value'];
        
        $strResult  = '(CatalogCondCtrlStoreQuantity::qStoreC($arProduct,'.$productCnt.'))=='.$logic;
    
        return  $strResult;        
        
    }
        
    // функция условие по которому проверяется, нужно ли применять скидку на корзину.
    //возратить надо true или false
    
    public static function qStoreC($arProduct,$cntProd = 1)
    {
        
        
        return false;
    }
}
?>
