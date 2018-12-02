<?php
define("LINE_CHANNEL_SECRET", "57a930dda63276a1755a3eb12d039bf9");
define("LINE_CHANNEL_TOKEN", "E3CZ8tmp8eBxSLJWGG19BbN2fluz1y+z0JaVaHHw4TbUQ8FQ/o7OhFlTL27vhEIzFIWcV08+dXFzWwCVoDBnGX5wL+i3zTWTti/ANAzy/uvp3LF0PNB/I3JXoGFUg/WcXeBh5/SOxolvxLp5i+x1DQdB04t89/1O/w1cDnyilFU=");

require_once(dirname(__FILE__).'/../vendor/autoload.php');
require_once(dirname(__FILE__).'/EventHandler.php');
require_once(dirname(__FILE__).'/EventHandler/MessageHandler/TextMessageHandler.php');
require_once(dirname(__FILE__).'/EventHandler/MessageHandler/LocationMessageHandler.php');
require_once(dirname(__FILE__).'/LinebotDAO.php');

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
use \LINE\LINEBot\Exception\InvalidSignatureException;
use \LINE\LINEBot\Exception\InvalidEventRequestException;

$bot = new LINEBot(new CurlHTTPClient(LINE_CHANNEL_TOKEN), ['channelSecret' => LINE_CHANNEL_SECRET,]);

$signature = $_SERVER["HTTP_" . HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");
// $bot->validateSignature($body, $signature);

try {
    $events = $bot->parseEventRequest($body, $signature);
} catch (InvalidSignatureException $e) {
    error_log("Exception orrured on parseEventRequest." . $e->getMessage());
} catch (InvalidEventRequestException $e) {
    error_log("Exception orrured on parseEventRequest." . $e->getMessage());
}

$dao = new LinebotDAO('localhost', 'linebot', 'tobot', 'P@ssw0rd');

// $json = json_decode($body);
// $event = $json->events[0];
// Username
// $profile = $event['source'];
// $userid = $profile['userId'];
// $eventType = $event['type'];

foreach ($events as $event) {
    $handler = null;

    // User Id
    $userid = $event->getUserId();
    
    if ($event instanceof MessageEvent) {
        $msgType = $event->getMessageType();
        
        // message event
        switch ($msgType) {
            case 'text':
                $type = "Text";
                $handler = new TextMessageHandler($bot, $event);
                $handler->handle();
                break;
            case 'sticker':
                $type = "Sticker";
                break;
            case 'location':
                $type = "Location";
                $handler = new LocationMessageHandler($bot, $event, $dao);
                $handler->handle();
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
    } else if ($event instanceof PostbackEvent) {
        // postback
        $type = $event->getType();
    } else {
        // Unsupported event
        error_log("Unsupported event type. [" . $event->getType() . "]");
    }
    
    $bot->replyText($event->getReplyToken(), 'Hi, ' . $userid . '. I got your ' . $type . 'message!');
    
}

        
