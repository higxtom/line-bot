<?php

require_once dirname(__FILE__).'/../../LinebotDAO.php';

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
    private $textMessage;
    private $dao;

    public function __construct($bot, TextMessage $textMessage, LinebotDAO $dao)
    {
        $this->bot = $bot;
        $this->textMessage = $textMessage;
        $this->dao = $dao;
    }

    public function handle()
    {
        $text = strtolower(trim($this->textMessage->getText()));
        $replyToken = $this->textMessage->getReplyToken();

        $userid = $this->textMessage->getUserId();

        // save command to database with userId;
        $this->dao->putReceivedCommand($userid, $text);

        switch ($text) {
            case 'profile':
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
                $postback = new PostbackTemplateActionBuilder('location', 'action=loc&res=loc', 'I am here!');
                $quickReply = new QuickReplyMessageBuilder([
                    new QuickReplyButtonBuilder(new LocationTemplateActionBuilder('Location')),
                    new QuickReplyButtonBuilder(new CameraTemplateActionBuilder('Camera')),
                    new QuickReplyButtonBuilder(new CameraRollTemplateActionBuilder('Camera roll')),
                    new QuickReplyButtonBuilder($postback),
                ]);
                $messageTemplate = new TextMessageBuilder('Where are you?', $quickReply);
                $this->bot->replyMessage($replyToken, $messageTemplate); break;

            case 'convenience_store':
                $postback = new PostbackTemplateActionBuilder('location', 'action=loc&res=loc', 'I am here!');
                $quickReply = new QuickReplyMessageBuilder([
                    new QuickReplyButtonBuilder(new LocationTemplateActionBuilder('Location')),
                    new QuickReplyButtonBuilder($postback),
                ]);
                $messageTemplate = new TextMessageBuilder('Where are you?', $quickReply);
                $this->bot->replyMessage($replyToken, $messageTemplate); break;

            case 'station':
                $postback = new PostbackTemplateActionBuilder('location', 'action=loc&res=loc', 'I am here!');
                $quickReply = new QuickReplyMessageBuilder([
                    new QuickReplyButtonBuilder(new LocationTemplateActionBuilder('Location')),
                    new QuickReplyButtonBuilder($postback),
                ]);
                $messageTemplate = new TextMessageBuilder('Where are you?', $quickReply);
                $this->bot->replyMessage($replyToken, $messageTemplate); break;

            case 'weather':
                $postback = new PostbackTemplateActionBuilder('location', 'action=loc&res=loc', 'I am here!');
                $quickReply = new QuickReplyMessageBuilder([
                    new QuickReplyButtonBuilder(new LocationTemplateActionBuilder('Location')),
                    new QuickReplyButtonBuilder($postback),
                ]);
                $messageTemplate = new TextMessageBuilder('Where are you?', $quickReply);
                $this->bot->replyMessage($replyToken, $messageTemplate); break;

            default:
                error_log('Unsupported event.'.$text);
        }
    }
}
