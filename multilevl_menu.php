<?
/**
делаем нормальную структуру данных вложенновсть. 
Можно запихнуть всё в цикл, но суть ясна. запихиваем это в result_modifier.php шаблона меню
*/
foreach($arResult as $key => $arItem) {
    if($arItem['IS_PARENT'] == 1 && $arItem['DEPTH_LEVEL'] == 3) {   //если есть дочерние элементы у элемента с уровнем вложенности 2
        for($k = $key+1; $k <= count($arResult)+50; $k++) {    //перебираем массив от следующего элемента до конца
        
            if($arResult[$k]['DEPTH_LEVEL'] == '4') { //если уровень вроженностти +1 от текущего то
            
                $arResult[$key]['CHILD_ITEMS'][] = $arResult[$k];    // добавляем этот элемент в родитель
                $arResult[$k] = '';            //присваиваем пустоту, чтоб не нарущать индексы, иначе всё сломается!
            } else {
                break;
            }
        } 
    }   
}

$arResult = array_diff($arResult, array(''));
$arResult = array_values($arResult);

foreach($arResult as $key => $arItem) {
    if($arItem['IS_PARENT'] == 1 && $arItem['DEPTH_LEVEL'] == 2) {   //если есть дочерние элементы у элемента с уровнем вложенности 2
        for($k = $key+1; $k <= count($arResult)+50; $k++) {    //перебираем массив от следующего элемента до конца
        
            if($arResult[$k]['DEPTH_LEVEL'] == '3') { //если уровень вроженностти +1 от текущего то
            
                $arResult[$key]['CHILD_ITEMS'][] = $arResult[$k];    // добавляем этот элемент в родитель
                $arResult[$k] = '';            //присваиваем пустоту, чтоб не нарущать индексы, иначе всё сломается!
            } else {
                break;
            }
        } 
    }   
}

$arResult = array_diff($arResult, array(''));
$arResult = array_values($arResult);

foreach($arResult as $key => $arItem) {
    if($arItem['IS_PARENT'] == 1 && $arItem['DEPTH_LEVEL'] == 1) {   //если есть дочерние элементы у элемента с уровнем вложенности 2
        for($k = $key+1; $k <= count($arResult)+50; $k++) {    //перебираем массив от следующего элемента до конца
        
            if($arResult[$k]['DEPTH_LEVEL'] == '2') { //если уровень вроженностти +1 от текущего то
            
                $arResult[$key]['CHILD_ITEMS'][] = $arResult[$k];    // добавляем этот элемент в родитель
                $arResult[$k] = '';            //присваиваем пустоту, чтоб не нарущать индексы, иначе всё сломается!
            } else {
                break;
            }
        } 
    }   
}

$arResult = array_diff($arResult, array(''));
$arResult = array_values($arResult);
