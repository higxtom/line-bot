<?php

function findStoreByGooglePlaces($latitude, $longitude, $types, $name = null, $radius = null)
{
    $API_Key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    $API_Url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';

    $Request = $API_Url;
    if ($latitude === null || $longitude === null || $types === null) {
        // error
    }

    $Request .= "&key=$API_Key&location=$latitude,$logitude&types=$types";
    if ($name != null) {
        $Request .= "&name=$name";
    }
    if ($radius != null) {
        $Request .= "&radius=$radius";
    }

    $options = [
        'http' => [
             'method' => 'GET',
             'timeout' => 3,
        ],
   ];

    $response = file_get_contents($Request, false, stream_context_create($options));
    if ($response == false) {
        echo 'Failed to access API.';

        return [];
    }

    return $response;
}
