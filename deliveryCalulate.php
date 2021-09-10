<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

function getDeliveryPriceForProduct($bitrixProductId, $siteId, $userId, $personTypeId, $userCityId)
{
    $arResult = [];

    \Bitrix\Main\Loader::includeModule('catalog');
    \Bitrix\Main\Loader::includeModule('sale');

    $basket = \Bitrix\Sale\Basket::create($siteId);

    $item = $basket->createItem('catalog', $bitrixProductId);
    $item->setFields(array(
        'QUANTITY' => 1,
        'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
        'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
    ));

    $order = \Bitrix\Sale\Order::create($siteId, $userId);
    $order->setPersonTypeId($personTypeId);
    $order->setBasket($basket);

    $orderProperties = $order->getPropertyCollection();
    $orderDeliveryLocation = $orderProperties->getDeliveryLocation();
    $orderDeliveryLocation->setField('VALUE', "0000073738");

    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    $shipment->setField('CURRENCY', $order->getCurrency());
    foreach ($order->getBasket() as $item)
    {
        $shipmentItem = $shipmentItemCollection->createItem($item);
        $shipmentItem->setQuantity($item->getQuantity());
    }
    $arDeliveryServiceAll = Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);

    if (!empty($arDeliveryServiceAll)) {
        foreach ($arDeliveryServiceAll as $oneDelivery) {
            $deliveryObj = $oneDelivery;

            if ($deliveryObj->isProfile()) {
                $name = $deliveryObj->getNameWithParent();
            } else {
                $name = $deliveryObj->getName();
            }

            $shipment->setFields(array(
                'DELIVERY_ID' => $deliveryObj->getId(),
                'DELIVERY_NAME' => $name,
                'CURRENCY' => $order->getCurrency()
            ));
            $res = $shipment->calculateDelivery();
            $price = $res->getPrice();
            $price_format = number_format($price, 0, '.', ' ') . ' ₽';
            $arResult[] = ['NAME' => $name, 'PERIOD' => $res->getPeriodDescription(), 'PRICE' => $price, 'PRICE_FORMAT' => $price_format];
        }
    }

    if (count($arResult) > 1) {
        uasort($arResult, function ($a, $b) {
            if ($a['PRICE'] == $b['PRICE']) {return 0;}
            return ($a['PRICE'] < $b['PRICE']) ? -1 : 1;
        });
    }

    return $arResult;
}

if (CTPLocation::$arData['LOCATION_ID'] && $_POST['PRODUCT_ID']) {
    $loc = \Bitrix\Sale\Location\LocationTable::getById(CTPLocation::$arData['LOCATION_ID'])->fetch();
    $deliveryPriceForProductCourier = getDeliveryPriceForProduct(
        htmlspecialcharsbx($_POST['PRODUCT_ID']),
        SITE_ID,
        1,
        1,
        $loc['CODE'] // Город пользователя
    );
}


