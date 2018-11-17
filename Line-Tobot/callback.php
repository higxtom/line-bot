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
// $json = json_decode($contents);
// $event = $json->events[0]; 

try {
    $events = $bot->parseEventRequest($contents, $signature);
    
    foreach ($events as $event) {        
        // Username
        $profile = ($bot->getProfile($event->source->userId));
        if ($profile->isSucceeded()) {
            $username = $profile->getJSONDecodedBody()['displayName'];
        }
        
        
        if ($event instanceof MessageEvent) {
            // message event
            if ($event instanceof TextMessage) {
                $type = "Text";
                continue;
            } else if ($event instanceof StickerMessage) {
                // sticker
                $type = "Sticker";
                continue;
            } else if ($event instanceof LocationMessage) {
                // location
                $type = "Location";
                continue;
            } else if ($event instanceof ImageMessage) {
                // image : unsupported yet now.
                $type = "Image";
                continue;
            } else if ($event instanceof AudioMessage) {
                // audio : unsupported yet now.
                $type = "Audio";
                continue;
            } else if ($event instanceof VideoMessage) {
                // video : unsupported yet now.
                $type = "Video";
                continue;
            } else {
                // unknown message
                $type = "Unknown, but a kind of ";
                error_log("Unsupoprted message event type.[" . $event->getMessageType() . "]");
                continue;
            }
        } else if ($event instanceof PostbackEvent) {
            // postback
            $type = "Postback event";
            continue;
        } else {
            // Unsupported event
            error_log("Unsupported event type. [" . $event->getType() . "]");
        }
        
        $bot->replyText($event->getReplyToken(), 'Hi, ' . $username . '. I got your ' . $type . 'message!');
        
    }
} catch (Exception $e) {
    error_log("Exception occurred on parsing events.");
}
