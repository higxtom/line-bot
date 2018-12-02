<?php 

define('CIRCUMFERENCE_OF_EARTH',6378150);

function getNearestStations($latitude, $longitude, $station_list, $distance) {
    $stations = json_decode($station_list);
    foreach ($stations as $station) {
        error_log($station['latitude'] . ":" . $station['longitude']);
        $dist = calcDistance($latitude, $longitude, $station['latitude'], $station['longitude']);
        error_log($station['station_name'] . " : " . $dist);
    }
}

// 緯度経度から２点間の距離を算出する。
// 近距離にある２点を対象とするため、球面座標の距離計測ではなく、
// 平面座標の距離計測（三平方の定理を活用）する。
// ただし、経度については緯度による１度あたりの距離が異なるため、それは考慮する。
function calcDistance($lat1, $lon1, $lat2, $lon2) {

    // 緯度・経度の一度あたりの距離（ｍ）を算出する。
    // 緯度１度の距離は、地球の円周を単純に３６０度で割って算出する。
    $dist_1d_lat = (2 * M_PI * CIRCUMFERENCE_OF_EARTH) / 360; 
    // 経度１度の距離は、算出する緯度での円周（赤道が最長）を考慮する。
    // ここで算出する距離は緯度はほぼ同じであるため、起点となる緯度・経度で算出する。
    $dist_1d_lon = (2 * M_PI * CIRCUMFERENCE_OF_EARTH * cos($lat1 / 180 * M_PI)) / 360;
    
    // 2点間の距離は、２点間の、緯度の差の２乗と経度の差の２乗を足した数値の平方根（３平方の定理より）
    $distance = sqrt( pow($dist_1d_lat * ($lat1 - $lat2), 2) + pow($dist_1d_lon * ($lon1 - $lon2), 2) );
    error_log("DISTANCE: " . $distance);
    
    return $distance;
}

?>