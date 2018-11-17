<?php
define("LINE_CHANNEL_SECRET", "");
define("LINE_CHANNEL_TOKEN", "");

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;

require('../vendor/autoload.php');

$bot = new LINEBot(new CurlHTTPClient(LINE_CHANNEL_TOKEN), ['channelSecret' => LINE_CHANNEL_SECRET,]);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$contents = file_get_contents("php://input");
$json = json_decode($contents);
$event = $json->events[0]; 

// Username
$profile = $event['source'];
$userid = $profile['userId'];        
        
$eventType = $event['type'];
if ($eventType === "message") {
    
    $message = $event['message'];
    $msgType = $message['type'];
    
    // message event
    switch ($msgType) {
        case 'text':
            $type = "Text";
            break;
        case 'sticker':
            $type = "Sticker";
            break;
        case 'location':
            $type = "Location";
            break;
        case 'image':
            $type = "Image";
            break;
        case 'audio':
            $type = "Audio";
            break;
        case 'video':
            $type = "Video";
            break;
        default:
            $type = "Unknown, but a kind of ";
            error_log("Unsupoprted message event type.[" . $event->getMessageType() . "]");
    }
} else if ($eventType === "postback") {
    // postback
    $type = "Postback event";
    continue;
} else {
    // Unsupported event
    error_log("Unsupported event type. [" . $eventType . "]");
}

$bot->replyText($event->getReplyToken(), 'Hi, ' . $userid . '. I got your ' . $type . 'message!');
        
