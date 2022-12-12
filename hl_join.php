<?use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

$entityKontragenty = HLBT::compileEntity(HLBT::getById(34)->fetch());
$entityPartnery = HLBT::compileEntity(HLBT::getById(36)->fetch());
$entityPolzovateli = HLBT::compileEntity(HLBT::getById(35)->fetch());

$arFilter = ['UF_INN' => '9909125356'];

$rsUsersU = $entityKontragenty->getDataClass()::getList([
    "select" => ['*',  'PARTNER_' => 'PARTNER.*', 'MANAGER_' => 'MANAGER.*'],
    "filter"=> $arFilter,
    "limit" => 1,
    'order' => array('ID' => 'ASC'),
    "runtime"=> array(
        'PARTNER' => array(
            'data_type' => $entityPartnery->getDataClass(),
            'reference' => array('=this.UF_PARTNER' => 'ref.UF_XML_ID'), 'join_type' => 'LEFT',
        ),
        'MANAGER' => array(
            'data_type' => $entityPolzovateli->getDataClass(),
            'reference' => array('=this.PARTNER.UF_OSNOVNOYMENEDZHER' => 'ref.UF_XML_ID'), 'join_type' => 'LEFT',
        )

    )
]);
