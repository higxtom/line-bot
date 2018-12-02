<?php
class linebotDAO
{
    private $pdo;
    
    public function __construct($host, $dbname, $dbuser, $dbpass) {
        try {
            $connstr = 'mysql:host='.$host.';dbname='.$dbname.';charset=utf8';
            $this->pdo = new PDO($connstr, $dbuser, $dbpass);
        } catch (PDOException $e) {
            error_log('Failed to connect to database['.$dbname.']' . $e->getMessage());
        }
    }
    
    public function findPrevCommandByUserId($userid) {
        try {
            // Get last command
            $sql = "select command, last_update from request_hist where userId = ? order by last_update desc";
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
}

