<?php
require_once(dirname(__FILE__).'/../../LinebotDAO.php');
require_once(dirname(__FILE__).'/../../utils/OpenWeather.php');
require_once(dirname(__FILE__).'/../../utils/StationData.php');

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Event\LeaveEvent;

// 駅検索時の緯度・経度のプラスマイナスする範囲
define('STATION_SEARCH_RANGE', 0.015);

class LocationMessageHandler implements EventHandler
{
    private $bot;
    private $locationMessage;
    private $logger;
    private $dao;
    
    public function __construct($bot, LocationMessage $locationMessage, LinebotDAO $dao) {
        $this->bot = $bot;
        $this->locationMessage = $locationMessage;
        //$this->logger = $logger;
        $this->dao = $dao;
    }
    
    public function handle() {
        $replyToken = $this->locationMessage->getReplyToken();
        $title = $this->locationMessage->getTitle();
        $address = $this->locationMessage->getAddress();
        $latitude = $this->locationMessage->getLatitude();
        $longitude = $this->locationMessage->getLongitude();
        
        $owm_json = getWeatherForecast($latitude, $longitude);
        $owm_data = json_decode($owm_json, true);
        //error_log($owm_data);
        
        $candidates = $this->dao->findStationsByCoordinates($latitude, $longitude, STATION_SEARCH_RANGE);
        //error_log($candidates);
        $nearest = json_decode(getNearestStations($latitude, $longitude, $candidates));
        if ( $nearest[2] === 0 ) {
            $rmsg = $nearest[0];
        } else {
            $rmsg = "The nearest station is " . $nearest[0] ."(" .$nearest[1] ."): " . number_format($nearest[2],0) ."m\\\n\\\n";
        }
        
        // 500 -> 1000 -> 1500
        $stations = json_decode(getStationsInRange($latitude, $longitude, $candidates, 500), true);
//         error_log(print_r($stations,true));
        $rmsg .= "Stations which are near from you are \\\n";
        foreach ($stations as $station) {
            error_log(print_r($station, true));
            $rmsg .= $station[0] . "(" . $station[1] . "): " . number_format($station[2],0) . "m\\\n";
        }
        
        $rmsg .= "\\\nYou are at " . $owm_data['name'] . ", and the weather focast is " . $owm_data['weather'][0]['main'] . '(' . $owm_data['weather'][0]['description'] . ')';
        error_log($rmsg);
        
        $this->bot->replyMessage(
            $replyToken,
            new TextMessageBuilder($rmsg)
//             new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}