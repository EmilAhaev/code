<? // /bitrix/modules/sale/lib/discount.php    2771   инклудим этот верехватчик в ядро
require_once ('CustomApplyDiscount.php');
if ($discount['ID'] == 12 || $discount['ID'] == 13) {
    $discount['APPLICATION'] = str_replace('\Bitrix\Sale\Discount\Actions::applyToBasket', 'CustomApplyDiscount::applyToBasket', $discount['APPLICATION']);
       
}



//перехватчик будет переправлять нашу корзину в кастомную функцию применения скидки, где городим наши условия


use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale;
    
CModule::IncludeModule('sale');

class CustomApplyDiscount extends \Bitrix\Sale\Discount\Actions 
{
        
    public static function applyToBasket(array &$order, array $action, $filter)
    {
         
        global $USER;
        
        if ($USER->IsAuthorized()) return;
        
        if (!function_exists('sortByPrice')) {
            function sortByPrice($a, $b)
            {
                if ($a['PRICE'] == $b['PRICE']) {
                    return 0;
                }
                return ($a['PRICE'] < $b['PRICE']) ? -1 : 1;
            }
        }
        uasort($order['BASKET_ITEMS'], "sortByPrice");
        
        
        static::increaseApplyCounter();
        if (!isset($action['VALUE']) || !isset($action['UNIT']))
            return;

        $orderCurrency = static::getCurrency();
        $value = (float)$action['VALUE'];
        $limitValue = (int)$action['LIMIT_VALUE'];
        $unit = (string)$action['UNIT'];
        $currency = (isset($action['CURRENCY']) ? $action['CURRENCY'] : $orderCurrency);
        $maxBound = false;
        if ($unit == self::VALUE_TYPE_FIX && $value < 0)
            $maxBound = (isset($action['MAX_BOUND']) && $action['MAX_BOUND'] == 'Y');
        $valueAction = (
            $value < 0
            ? Sale\OrderDiscountManager::DESCR_VALUE_ACTION_DISCOUNT
            : Sale\OrderDiscountManager::DESCR_VALUE_ACTION_EXTRA
        );

        $actionDescription = array(
            'ACTION_TYPE' => Sale\OrderDiscountManager::DESCR_TYPE_VALUE,
            'VALUE' => abs($value),
            'VALUE_ACTION' => $valueAction
        );
        switch ($unit)
        {
            case self::VALUE_TYPE_SUMM:
                $actionDescription['VALUE_TYPE'] = Sale\OrderDiscountManager::DESCR_VALUE_TYPE_SUMM;
                $actionDescription['VALUE_UNIT'] = $currency;
                break;
            case self::VALUE_TYPE_PERCENT:
                $actionDescription['VALUE_TYPE'] = Sale\OrderDiscountManager::DESCR_VALUE_TYPE_PERCENT;
                break;
            case self::VALUE_TYPE_FIX:
                $actionDescription['VALUE_TYPE'] = Sale\OrderDiscountManager::DESCR_VALUE_TYPE_CURRENCY;
                $actionDescription['VALUE_UNIT'] = $currency;
                if ($maxBound)
                    $actionDescription['ACTION_TYPE'] = Sale\OrderDiscountManager::DESCR_TYPE_MAX_BOUND;
                break;
            default:
                return;
                break;
        }

        if(!empty($limitValue))
        {
            $actionDescription['ACTION_TYPE'] = Sale\OrderDiscountManager::DESCR_TYPE_LIMIT_VALUE;
            $actionDescription['LIMIT_TYPE'] = Sale\OrderDiscountManager::DESCR_LIMIT_MAX;
            $actionDescription['LIMIT_UNIT'] = $orderCurrency;
            $actionDescription['LIMIT_VALUE'] = $limitValue;
        }

        static::setActionDescription(self::RESULT_ENTITY_BASKET, $actionDescription);

        if (empty($order['BASKET_ITEMS']) || !is_array($order['BASKET_ITEMS']))
            return;

        static::enableBasketFilter();
        
        $ITEM_COUNT_IN_BASKET = count($order['BASKET_ITEMS']);
        if ($ITEM_COUNT_IN_BASKET < 2) 
            return;
        
        if ($action['VALUE'] == -10) {
            if ($ITEM_COUNT_IN_BASKET == 2) {
                //даем скидку 10% на первый (самый дешевый ) товар
                $num = 1;
                foreach ($order['BASKET_ITEMS'] as $productId => $Item) {
                    if ($num == 1) {  
                        
                        $applyBasket = array($productId => $Item);  
                    }
                    $num++;    
                }   
            } elseif ($ITEM_COUNT_IN_BASKET >= 3) {
                //делаем скидку 10% на второй по дешивезне заказ
                
                $num = 1;
                foreach ($order['BASKET_ITEMS'] as $productId => $Item) {
                    if ($num == 2) {  
                        
                        $applyBasket = array($productId => $Item);  
                    }
                    $num++;    
                }    
            }     
        }
         
        if ($action['VALUE'] == -20) {
            if ($ITEM_COUNT_IN_BASKET >= 3) {
                //скидка 20% на самый дешевый
                $num = 1;
                foreach ($order['BASKET_ITEMS'] as $productId => $Item) {
                    if ($num == 1) {  
                        $applyBasket = array($productId => $Item);  
                    }
                    $num++;    
                }        
            }
        }
        
    
        /*
        $filteredBasket = static::getBasketForApply($order['BASKET_ITEMS'], $filter, $action);
       
        if (empty($filteredBasket))
            return;
        */           
       
        //$applyBasket = array_filter($filteredBasket, '\Bitrix\Sale\Discount\Actions::filterBasketForAction');
        
        
        unset($filteredBasket);
        if (empty($applyBasket))
            return;
        
        
        if ($unit == self::VALUE_TYPE_SUMM || $unit == self::VALUE_TYPE_FIX)
        {
            if ($currency != $orderCurrency)
                /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
                $value = \CCurrencyRates::convertCurrency($value, $currency, $orderCurrency);
            if ($unit == self::VALUE_TYPE_SUMM)
            {
                $value = static::getPercentByValue($applyBasket, $value);
                if (
                    ($valueAction == Sale\OrderDiscountManager::DESCR_VALUE_ACTION_DISCOUNT && ($value >= 0 || $value < -100))
                    ||
                    ($valueAction == Sale\OrderDiscountManager::DESCR_VALUE_ACTION_EXTRA && $value <= 0)
                )
                    return;
                $unit = self::VALUE_TYPE_PERCENT;
            }
        }
        $value = static::roundZeroValue($value);
        if ($value == 0)
            return;

        foreach ($applyBasket as $basketCode => $basketRow)
        {
            list($calculateValue, $result) = self::calculateDiscountPrice(
                $value,
                $unit,
                $basketRow,
                $limitValue,
                $maxBound
            );
            if ($result >= 0)
            {
                if (!isset($basketRow['DISCOUNT_PRICE']))
                    $basketRow['DISCOUNT_PRICE'] = 0;
                $basketRow['PRICE'] = $result;
                if (isset($basketRow['PRICE_DEFAULT']))
                    $basketRow['PRICE_DEFAULT'] = $result;
                $basketRow['DISCOUNT_PRICE'] -= $calculateValue;

                $order['BASKET_ITEMS'][$basketCode] = $basketRow;

                $rowActionDescription = $actionDescription;
                $rowActionDescription['BASKET_CODE'] = $basketCode;
                $rowActionDescription['RESULT_VALUE'] = abs($calculateValue);
                $rowActionDescription['RESULT_UNIT'] = $orderCurrency;

                if(!empty($limitValue))
                {
                    $rowActionDescription['ACTION_TYPE'] = Sale\OrderDiscountManager::DESCR_TYPE_LIMIT_VALUE;
                    $rowActionDescription['LIMIT_TYPE'] = Sale\OrderDiscountManager::DESCR_LIMIT_MAX;
                    $rowActionDescription['LIMIT_UNIT'] = $orderCurrency;
                    $rowActionDescription['LIMIT_VALUE'] = $limitValue;
                }

                static::setActionResult(self::RESULT_ENTITY_BASKET, $rowActionDescription);
                unset($rowActionDescription);
            }
            unset($result);
        }
        unset($basketCode, $basketRow);
    }
}
