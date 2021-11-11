<?
//в init.php 
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'onSalePaySystemRestrictionsClassNamesBuildList',
    'myPayFunction'
);
function myPayFunction()
{
    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\MyPayRestriction' => '/local/class/mypayrestriction.php',
        )
    );
}

// в /local/class/mypayrestriction.php

<?use Bitrix\Sale\Delivery\Restrictions;
use Bitrix\Sale\Internals\Entity;

class MyPayRestriction extends Restrictions\Base
{
    public static function getClassTitle()
    {
        return 'Нет товаров из выбранного инфоблока';
    }

    public static function getClassDescription()
    {
        return 'оплата будет выводится только при отстутствии товаров из выбранного инфоблока';
    }

    public static function check($paymentData,  $restrictionParams, $deliveryId = 0)
    {
        $result = true;
        if ($restrictionParams['IB_ID'] && !empty($paymentData) && count($paymentData) > 0) {
            $stopIbId = $restrictionParams['IB_ID'];
            if (in_array($stopIbId, $paymentData)) {
                $result = false;
            }
        }
        return $result;
    }
    protected static function extractParams(Entity $payment)
    {
        //$collection = $payment->getCollection();
        //$order = $collection->getOrder();

        $result = [];

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
        $basketItems = $basket->getBasketItems();
        $arProductIds = [];
        foreach ($basketItems as $basketItem) {
            $arProductIds[] = $basketItem->getProductId();
        }
        if (count($arProductIds) > 0) {
            $arSelect = ['IBLOCK_ID'];
            $arFilter = ['ID' => $arProductIds];
            $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            $result = [];
            while ($ob = $res->Fetch()) {
                $result[] = $ob['IBLOCK_ID'];
            }
        }
        
        return $result;
    }
    public static function getParamsStructure($entityId = 0)
    {
        return array(
            "IB_ID" => array(
                'TYPE' => 'NUMBER',
                'DEFAULT' => "",
                'LABEL' => 'Инфоблок'
            )
        );
    }
}
