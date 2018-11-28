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
    private $textMessage;
    
    public function __construct($bot, TextMessage $textMessage)
    {
        $this->bot = $bot;
        $this->textMessage = $textMessage;
    }

    public function handle()
    {
        $text = $this->textMessage->getText();
        $replyToken = $this->textMessage->getReplyToken();

        $userid = $this->textMessage->getUserId();
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=linebot;charset=utf8', 'tobot', 'P@ssw0rd');
            // Get last command
            $sql = "select command from request_hist where userId = ? order by last_update desc";
            $pstmt = $pdo->prepare($sql);
            $pstmt->execute(array($userid));
            while ($result = $pstmt->fetch(PDO::FETCH_ASSOC)) {
                $prev_command = $result['command'];
                error_log($prev_command);
            }
            error_log("db access succeeded.")
        } catch(PDOException $e) {
            error_log('Error has occurred on accesing database' . $e->getMessage());
        }
        
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