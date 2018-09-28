<? //оч крутая штука, пересчитывает заказ и применяет если надо скидки купоны и прочую радость 
// было найдено тут https://blog.budagov.ru/pereraschet-zakaza-na-api/
//возможно что-то похожее /bitrix/modules/sale/admin/order_edit.php
\Bitrix\Main\Loader::includeModule('sale');

$order = \Bitrix\Sale\Order::load($orderId); // или $order = \Bitrix\Sale\Order::loadByAccountNumber($orderNumber);

$discount = $order->getDiscount();
\Bitrix\Sale\DiscountCouponsManager::clearApply(true);
\Bitrix\Sale\DiscountCouponsManager::useSavedCouponsForApply(true);
$discount->setOrderRefresh(true);
$discount->setApplyResult(array());

/** @var \Bitrix\Sale\Basket $basket */
if (!($basket = $order->getBasket())) {
   throw new \Bitrix\Main\ObjectNotFoundException('Entity "Basket" not found');
}

$basket->refreshData(array('PRICE', 'COUPONS'));
$discount->calculate();
$order->save();
