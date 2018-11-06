<?php

function getWeatherForecast($latitude, $longitude) {
    $API_Key = '86862e396797de5f5c1a11fb28a89787';
    $API_BaseUrl="https://api.openweathermap.org/data/2.5/weather";
    $API_URL="$API_BaseUrl?lat=$latitude&lon=$longitude&appid=$API_Key";

    $options = [
         'http' => [
              'method' => 'GET',
              'timeout' => 3,
         ]
    ];
 
    $response = file_get_contents($API_URL, false, stream_context_create($options));
    if ($response == false) {
         echo "Failed to access API.";
         return [];
    }

    return $response;
}

?>

