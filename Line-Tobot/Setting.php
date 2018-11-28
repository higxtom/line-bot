<?php

class Setting
{
    public static function getSetting()
    {
        return [
            'displayErrorDetails' => true,
            'logger' => [
                'name' => 'line-tobot',
                'path' => __DIR__ . "../logs/line-bot.log",
            ],
            'bot' => [
                'channelToken' => getenv('LINEBOT_CHANNEL_TOKEN') ?: 'E3CZ8tmp8eBxSLJWGG19BbN2fluz1y+z0JaVaHHw4TbUQ8FQ/o7OhFlTL27vhEIzFIWcV08+dXFzWwCVoDBnGX5wL+i3zTWTti/ANAzy/uvp3LF0PNB/I3JXoGFUg/WcXeBh5/SOxolvxLp5i+x1DQdB04t89/1O/w1cDnyilFU=',
                'channelSecret' => geten('LINEBOT_CHANNEL_SECRET') ?: '57a930dda63276a1755a3eb12d039bf9'
            ],
            'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
        ];
    }
}