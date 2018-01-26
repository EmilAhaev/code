<?
/*
 * создаем новое подключение к API Instagram, о том, как получить $token, написано выше;
 */
$token = '2114372025.89e9dd4.1c7577eebbf34afeb68ef341722ff00e';
/*
 * Тут указываем либо ID пользователя, либо "self" для вывода фото владельца токена
 * Как получить ID? Да в том же инструменте, в котором вы получали токен
 */
 
// при каждой загрузке страницы ждать фотки с инстна слишком жирно, кешируем это дело на часов 6

$life_time = 6*60*60;
$cache_id = 'ista_photos'; //имя менять не будем, пусть кеш просто обновляется раз в 6 часов, ну или пока не сбросят
$obCache = new CPHPCache;
if($obCache->InitCache($life_time, $cache_id, "/"))
{
   $media = $obCache->GetVars(); 
}
elseif($obCache->StartDataCache())
{
       
    $user_id = 'self'; // если хотим получать фотки только  хозяина токина
    $instagram_cnct = curl_init(); // инициализация cURL подключения
    curl_setopt( $instagram_cnct, CURLOPT_URL, "https://api.instagram.com/v1/users/" . $user_id . "/media/recent?access_token=" . $token ); // подключаемся
    curl_setopt( $instagram_cnct, CURLOPT_RETURNTRANSFER, 1 ); // просим вернуть результат
    curl_setopt( $instagram_cnct, CURLOPT_TIMEOUT, 15 );
    $media = json_decode( curl_exec( $instagram_cnct ) ); // получаем и декодируем данные из JSON
    curl_close( $instagram_cnct ); // закрываем соединение
    $obCache->EndDataCache($media);
} 
/*
 * количество фотографий для вывода
 */
$limit = 30;
/*
 * размер изображений (высота и ширина одинаковые)
 */
$size = 265;
?>

<?if(is_array($media->data) && !empty($media->data)) { ?>

    <div class="container section-animate">
        <div class="section-carousel section-carousel_wthout-text">
            
            <div class="carousel-block">
                <div class="carousel-block__header">
                    <div class="carousel-block__img">
                        <img src="<?= SITE_TEMPLATE_PATH?>/img/base/img-instagramm.png" alt="">
                    </div>
                    <a target="_blank" href="<?= COption::GetOptionString( "askaron.settings", "UF_INSTA_LINK" )?>" class="carousel-block__title">    
                        МЫ в INSTAGRAM
                    </a>
                </div>
                <div class="carousel-block__body">
                    Подписывайтесь на наш паблик в инстаграм и следите за нашими работами
                </div>
            </div>
            
            <div class="carousel-slider">
                <?$photos = $media->data;?>
                <?for ($i = 0; $i < 15; $i = $i + 2 ) {?>
                    
                    <div class="slide">
                        <div class="wrap-slide-media">
                            <a data-fancybox="group-insta" href="<?= $photos[$i]->images->standard_resolution->url;?>" class="b-slide-media fancy">
                                <div class="b-slide-media__photo">
                                    <img src="<?= $photos[$i]->images->low_resolution->url?>" alt="">
                                    <div class="btn-round-plus">+</div>
                                </div>
                            </a>
                            <?$j = $i+1;?>
                            <a data-fancybox="group-insta" href="<?= $photos[$j]->images->standard_resolution->url;?>" class="b-slide-media fancy">
                                <div class="b-slide-media__photo">
                                    <img src="<?= $photos[$j]->images->low_resolution->url?>" alt="">
                                    <div class="btn-round-plus">+</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                <?}?>
            </div>
        </div>    
    </div>
<?}?>
