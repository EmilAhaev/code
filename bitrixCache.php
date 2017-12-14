<?php 
$life_time = 6*60*60; //время кеша в секундах
$cache_id = 'section_description' . $intSectionID; //уникализируем как нам надо id кеша
$obCache = new CPHPCache;
if ($obCache->InitCache($life_time, $cache_id, "/")) {
   $description = $obCache->GetVars(); 
} elseif ($obCache->StartDataCache()) {           
    $arFilter = ['ID' => $intSectionID, 'IBLOCK_ID' => $arParams["IBLOCK_ID"]];
    $arSelect = ['DESCRIPTION'];
    $rsSections = CIBlockSection::GetList(array(), $arFilter, false, $arSelect, false);        
    if ($section = $rsSections->GetNext()) {
        $description = $section['DESCRIPTION'];     
    }
    $obCache->EndDataCache($description);
}
