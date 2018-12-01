<?php
require(dirnae(__FILE__)."/../../OpenWeather.php");

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
        
        $owm_json = getWeatherForecast($latitude, $longitude);
        $owm_data = json_decode($owm_json, true);
        error_log($owm_data);
        
        $rmsg = "You are at" . $owm_data['name'] . ", and the weather focast is " . $owm_data['weather'][0]['main'] . '(' . $own_data['weather'][0]['description'];
        error_log($rmsg);
        
        $this->bot->replyMessage(
            $replyToken,
            new TextMessageBuilder($rmsg)
//             new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}