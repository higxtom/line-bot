<?php
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
        
        $rmsg = "You are at '" . $title . "', right, and the place address is " . $address . ", and the cordinates is " . $latitude . ":" . $longitude;
        
        $this->bot->replyMessage(
            $replyToken,
            new TextMessageBuilder($rmsg)
//             new LocationMessageBuilder($title, $address, $latitude, $longitude)
        );
    }
}