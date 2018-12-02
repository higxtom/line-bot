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
    
    public function findPrevCommandByUserId($userid) {
        $sql = "select command, last_update from request_hist where userId = ? order by last_update desc";
        try {
            // Get last command
            $pstmt = $this->pdo->prepare($sql);
            $pstmt->execute(array($userid));
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
        $sql = "select s.station_name, l.line_name, s.logitude, s.latitude";
        $sql .= " from station s, line l";
        $sql .= " where s.railline_cd = l.line_cd";
        $sql .= " and latitude between ? and ?";
        $sql .= " and longitude between ? and ?";
        
        // 検索条件の緯度・経度の計算（引数で受け取った検索範囲を条件にする）
        $lat_s = $latitude - $range;
        $lat_e = $latitude + $range;
        $lon_s = $longitude - $range;
        $lon_e = $longitude + $range;
        
        try {
            $pstmt = $this->pdo->prepare($sql);
            $pstmt->execute(array($lon_s, $lon_e, $lat_s, $lat_e));
            if ( $pstmt->rowCount() > 0 ) {
                $stt_list = json_encode($pstmt->fetchAll(PDO::FETCH_ASSOC));
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

