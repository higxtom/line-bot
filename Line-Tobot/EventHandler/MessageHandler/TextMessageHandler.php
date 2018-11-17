<?php
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraRollTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\LocationTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;

class TextMessageHandler implements EventHandler
{
    private $bot;
//     private $logger;
    private $req;
    private $textMessage;
    
    public function __construct($bot, $req, TextMessage $textMessage)
    {
        $this->bot = $bot;
        $this->req = $req;
        $this->textMessage = $textMessage;
    }

    public function handle()
    {
        $text = $this->textMessage->getText();
        $replyToken = $this->textMessage->getReplyToken();

        switch ($text) {
            case 'profile':
                $userid = $this->textMessage->getUserId();
                $this->sentProfile($replyToken, $userid);
                break;
            case 'language':
                $this->bot->replyMessage($replyToken,
                    new TemplateMessageBuilder('Confirm language', 
                        new ConfirmTemplateBuilder('Which language do you prefer?', [
                                new MessageTemplateActionBuilder('en', 'English'),
                                new MessageTemplateActionBuilder('ja', '日本語'),
                        ])
                    )
                );
                break;
            case 'bar':
                $postback = new PostbackTemplateActionBuilder('location', 'action=loc&res=loc','I am here!');
                $quickReply = new QuickReplyMessageBuilder([
                    new QuickReplyButtonBuilder(new LocationTemplateActionBuilder('Location')),
                    new QuickReplyButtonBuilder(new CameraTemplateActionBuilder('Camera')),
                    new QuickReplyButtonBuilder(new CameraRollTemplateActionBuilder('Camera roll')),
                    new QuickReplyButtonBuilder($postback),
                ]);
                $messageTemplate = new TextMessageBuilder('Where are you?', $quickReply);
                $this->bot->replyMessage($replyToken, $messageTemplate);break;
            default:
                error_log("Unsupported event." . $text);
                
        }
    }
}