<?
//в show() может быть данные о редактируемых данных, тогда надо обрабатывать еще удаление файлов, возможно там можно вывести fileId 

echo \Bitrix\Main\UI\FileInput::createInstance(array(
    "name" => 'FILES[n#IND#]',
    "description" => false,
    "upload" => true,
    "allowUpload" => "A",
    "medialib" => false,
    "fileDialog" => true,
    "cloud" => true,
    "delete" => true,
    "maxCount" => 10
))->show();
?>

<?
//результат будет в $_REQUEST['FILES']
if (isset($POST['FILES']) && is_array($POST['FILES'])) {
    foreach ($POST['FILES'] as $key => $file) {
        if(defined("BX_TEMPORARY_FILES_DIRECTORY")) {
            $tmpPath = BX_TEMPORARY_FILES_DIRECTORY;
        } else {
            $tmpPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/tmp';
        }
        $file['tmp_name'] = $tmpPath . $file['tmp_name'];
        $PROP['FILES'][] = [
            'VALUE' => $file,
            'DESCRIPTION' => ''
        ];
    }
}
?>
