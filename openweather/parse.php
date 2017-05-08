<?
$cityId = 694423; //sevastopol id
$apiKey = 'f94576ee92c7cc2eabdffc1ea6a38fd6';

$str = file_get_contents ('http://api.openweathermap.org/data/2.5/forecast?id=' . $cityId . '&lang=ru&units=metric&appid=' . $apiKey);

$data = json_decode($str, true);

foreach ($data['list'] as $key => $value) { //раскидаем погоду по дням
    $date = date('d-m', strtotime($value['dt_txt'])); //берем именно отсюда потому что в dt какая-то лажа 
    $weathForDay[$date][] = $value;
}

echo "<pre>"; print_r($weathForDay); echo "</pre>";
