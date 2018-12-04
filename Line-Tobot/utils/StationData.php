<?php 

define('CIRCUMFERENCE_OF_EARTH',6378150);

function getNearestStations($latitude, $longitude, $station_list) {
    $stations = json_decode($station_list);
    $nearest = array("指定範囲には駅がありませんでした。", "", 0 );
    
    $prev_dist = 10000; // 10000m = 10km
    foreach ($stations as $station) {
        $dist = calcDistance($latitude, $longitude, $station->latitude, $station->longitude);
        error_log(number_format($prev_dist,0) . " <-> " . number_format($dist));
        if ($dist < $prev_dist) {
            $nearest = array($station->station_name, $station->line_name, $station->latitude, $station->longitude, $dist);
            error_log($station->station_name . "(" . $station->line_name . "): " . number_format($dist, 3));
            $prev_dist = $dist;
        }
        error_log("<pre> : " . $prev_dist);
    }
    if ($nearest[2] === 0) {
        $nearest = array("少なくとも10km以内には駅がありませんでした。", "", 0, 0, 0 );
        error_log("少なくとも10km以内には、駅がありませんでした。");
    }
    return json_encode($nearest);
}

function getStationsInRange($latitude, $longitude, $station_list, $distance) {
    $stations = json_decode($station_list);
    $list = array();
    
    if (count($stations) === 0) {
        array_push($list, array("指定範囲には駅がありませんでした。", "範囲:".$distance."m", 0 ));
        error_log("指定された範囲には、駅がありませんでした。");
    } else {
        foreach ($stations as $station) {
            $dist = calcDistance($latitude, $longitude, $station->latitude, $station->longitude);
            if ($dist < $distance) {
                array_push($list, array($station->station_name, $station->line_name, $dist));
                error_log($station->station_name . "(" . $station->line_name . "): " . number_format($dist, 3));
            }
        }
        if (count($list) === 0) {
            array_push($list, array("指定範囲には駅がありませんでした。", "範囲:".$distance."m", 0 ));
            error_log("指定された範囲には、駅がありませんでした。");
        }
    }
    return json_encode($list);
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
//     error_log("DISTANCE: " . $distance);
    
    return $distance;
}

?>