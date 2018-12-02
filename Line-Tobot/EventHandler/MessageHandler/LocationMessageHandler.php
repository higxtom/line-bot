<?php
require_once(dirname(__FILE__).'/../../utils/OpenWeather.php');
require_once(dirname(__FILE__).'/../../LinebotDAO.php');

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Event\LeaveEvent;

// 駅検索時の緯度・経度のプラスマイナスする範囲
define('STATION_SEARCH_RANGE', 0.02);

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
        
        $stations = $dao->findStationsByCoordinates($latitude, $longitude, STATION_SEARCH_RANGE);
        error_log($stations);
        
        $rmsg = "You are at" . $owm_data['name'] . ", and the weather focast is " . $owm_data['weather'][0]['main'] . '(' . $owm_data['weather'][0]['description'];
        error_log($rmsg);
        
        $this->bot->replyMessage(
            $replyToken,
            new TextMessageBuilder($rmsg)
//             new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}