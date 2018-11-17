<?php
use LINE\LINEBot;
use LINE\LINEBot\Event\PostbackEvent;
use EventHandler;

class PostEventHandler implements EventHandler
{
    private $bot;
//     private $logger;
    private $postbackEvent;
    
    public function __construct($bot, PostbackEvent $postbackEvent)
    {
        $this->bot = $bot;
//         $this->logger = $logger
        $this->postbackEvent = $postbackEvent;
        
    }
    
    public function handle()
    {
        $this->bot->replyText(
          $this->bot->getReplyToken(), 'Got postback ' . $this->postbackEvent->getPostbackData()
        );
    }
}