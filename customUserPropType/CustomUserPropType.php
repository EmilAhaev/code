<?AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CCustomTypeStuff', 'GetUserTypeDescription')); 

class CCustomTypeStuff 
{

   //описываем поведение пользовательского свойства
   function GetUserTypeDescription() 
   {
       return array(
         'PROPERTY_TYPE'           => 'S',
         'USER_TYPE'              => 'stuff',
         'DESCRIPTION'            => '3 поля',
         'GetPropertyFieldHtml'   => array('CCustomTypeStuff', 'GetPropertyFieldHtml'),
         'ConvertToDB'            => array('CCustomTypeStuff', 'ConvertToDB'),
         'ConvertFromDB'          => array('CCustomTypeStuff', 'ConvertFromDB')
       );
   }

   //формируем поля
   function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) 
   {

       $value['DESCRIPTION'] = unserialize($value['DESCRIPTION']);

       $return =   '';
       $return .=  '<div style="float:left; margin-right:24px;"><label for="f1">Описание 1</label><br><input type="text" size="30" name="'.$strHTMLControlName['DESCRIPTION'].'[DESC1]"  id="'.$strHTMLControlName['DESCRIPTION'].'[DESC1]" value="'.$value['DESCRIPTION']['DESC1'].'"></div>';
       $return .=  '<div><label for="f3">Описание 2</label><br><input type="text" size="30"name="'.$strHTMLControlName['DESCRIPTION'].'[DESC2]" id="'.$strHTMLControlName['DESCRIPTION'].'[DESC2]" value="'.$value['DESCRIPTION']['DESC2'].'"></div>';
       $return .=  '<div><label for="f3">Значение:</label><br><textarea type="text" rows="1" cols="70" name="'.$strHTMLControlName['VALUE'].'" id="'.$strHTMLControlName['VALUE'].'" value="'.$strHTMLControlName['VALUE'].'" >'.$value['VALUE'].'</textarea></div><br>';
       $return .=  '';

       return  $return;
   }

   //сохраняем в базу
   function ConvertToDB($arProperty, $value)
   {  
   $value['DESCRIPTION'] = serialize($value['DESCRIPTION']);  
       return $value;
   }

   //читаем из базы
   function ConvertFromDB($arProperty, $value)
   {         
       $value['DESCRIPTION'] = 'test1';                       
       return $value;
   }

}