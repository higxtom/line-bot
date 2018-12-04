<?php
class LinebotDAO
{
    private $pdo;
    
    // コンストラクタ
    public function __construct($host, $dbname, $dbuser, $dbpass) {
        try {
            $connstr = 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8';
            $this->pdo = new PDO($connstr, $dbuser, $dbpass);
        } catch (PDOException $e) {
            error_log('Failed to connect to database['.$dbname.']' . $e->getMessage());
        }
    }
    
    public function putReceivedCommand($userid, $command) {
        $sql = "insert into command (user_id, command) values (:userid, :command)";
        $sql .= " on duplicate key update command = :command";
        
        try {
            $pstmt = $this->pdo->prepare($sql);
            $pstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
            $pstmt->bindParam(':command', $command, PDO::PARAM_STR);
            $pstmt->execute();
            
        } catch (PDOException $e) {
            error_log('Error has occurred on accessing database'.$e->getMessage());
        }
    }
    
    public function findPrevCommandByUserId($userid) {
        
        error_log("USERID:".$userid);
        $sql = "select command, last_update from request_hist where user_id = :userid";
        try {
            // Get last command
            $pstmt = $this->pdo->prepare($sql);
            $pstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
            $pstmt->execute();
            while ($result = $pstmt->fetch(PDO::FETCH_ASSOC)) {
                $prev_command = $result['command'];
                error_log($prev_command);
            }
            error_log("db access succeeded.");
        } catch(PDOException $e) {
            error_log('Error has occurred on accesing database' . $e->getMessage());
        }   
    }
    
    // 駅データDBを緯度・経度から取得し、JSON形式で結果を返す。
    public function findStationsByCoordinates($latitude, $longitude, $range) {
        // 検索SQL 緯度経度を条件に駅名、路線名、駅の緯度と経度を取得する
        $sql = "select s.station_name, l.line_name, s.longitude, s.latitude";
        $sql .= " from station s, line l";
        $sql .= " where s.railline_cd = l.line_cd";
        $sql .= " and s.latitude between :lat_X and :lat_Y";
        $sql .= " and s.longitude between :lon_X and :lon_Y";
        
        // 検索条件の緯度・経度の計算（引数で受け取った検索範囲を条件にする）
        $lat_x = $latitude - $range;
        $lat_y = $latitude + $range;
        $lon_x = $longitude - $range;
        $lon_y = $longitude + $range;
        
//         error_log("SQL: ". $sql);
//         error_log("LAT:" . $lat_s . " - " . $latitude . " - " . $lat_e);
//         error_log("LON:" . $lon_s . " - " . $longitude . " - " . $lon_e);
        
        try {
            $pstmt = $this->pdo->prepare($sql);
            $pstmt->bindParam(':lat_X', $lat_x);
            $pstmt->bindParam(':lat_Y', $lat_y);
            $pstmt->bindParam(':lon_X', $lon_x);
            $pstmt->bindParam(':lon_Y', $lon_y);
            $pstmt->execute();
//             $pstmt->execute(array($lat_s, $lat_e, $lon_s, $lon_e));
            
            error_log("RS: " . $pstmt->rowCount());
            if ( $pstmt->rowCount() > 0 ) {
                $stt_list = json_encode($pstmt->fetchAll(PDO::FETCH_ASSOC));
//                 error_log($stt_list);
            } else {
                error_log("got no record.Check DB record or sql condition.");
                $stt_list = null;
            }
            return $stt_list;
        } catch (PDOException $e) {
            error_log('Error has occurred on accesing Station'.$e->getMessage());
        }
    }
        
}

