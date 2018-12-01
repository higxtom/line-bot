<?php
require_once('../../OpenWeather.php');

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LocationMessageHandler implements EventHandler
{
    private $bot;
    private $locationMessage;
    private $logger;
    
    public function __construct($bot, LocationMessage $locationMessage) {
        $this->bot = $bot;
        $this->locationMessage = $locationMessage;
        //$this->logger = $logger;
    }
    
    public function handle() {
        $replyToken = $this->locationMessage->getReplyToken();
        $title = $this->locationMessage->getTitle();
        $address = $this->locationMessage->getAddress();
        $latitude = $this->locationMessage->getLatitude();
        $longitude = $this->locationMessage->getLongitude();
        
        $rmsg = "the place you are now is " . $address . ", and the cordinates is " . $latitude . ":" . $longitude;
        error_log($rmsg);
        
        $owm_json = getWeatherForecast($latitude, $longitude);
        $owm_data = json_decode($owm_json, true);
        error_log($owm_data);
        
        $this->bot->replyMessage(
            $replyToken,
            new TextMessageBuilder($rmsg)
//             new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}