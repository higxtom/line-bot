<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');
require_once('./OpenWeather.php');
require_once('./BarInfo.php');

$channelAccessToken = 'E3CZ8tmp8eBxSLJWGG19BbN2fluz1y+z0JaVaHHw4TbUQ8FQ/o7OhFlTL27vhEIzFIWcV08+dXFzWwCVoDBnGX5wL+i3zTWTti/ANAzy/uvp3LF0PNB/I3JXoGFUg/WcXeBh5/SOxolvxLp5i+x1DQdB04t89/1O/w1cDnyilFU=';
$channelSecret = '57a930dda63276a1755a3eb12d039bf9';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    $input = strtolower(trim($message['text']));
                    $title = "Sorry, your order doesn't work for me now.";
                    $info = "Thanks anyway!";
                    $st_pack = 11538;
                    $sticker = 51626501;

                    if (preg_match("/^bar:/", $input)) {
                       $title = "Bar Information";
                       $info = "";
                       $st_pack = 4;
                       $sticker = 300;

                       $barlist = json_decode(getBarInfoByArea(ltrim($input, 'bar:')), true);
                       foreach ($barlist as $bar) {
                          $info .= $bar['name'];
                          $info .= "("; 
                          $info .= $bar['url']; 
                          $info .= ")\n";
                       }
                    }
                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'sticker',
                                'packageId' => $st_pack,
                                'stickerId' => $sticker
                            ),
                            array(
                                'type' => 'text',
                                'text' => $title
                            ),
                            array(
                                'type' => 'text',
                                'text' => $info
                            )
                        )
                    ));
                    break;
                case 'location':
                    $owm_json = getWeatherForecast($message['latitude'], $message['longitude']);
                    $owm_data = json_decode($owm_json, true);

                    $client->replyMessage(array(
                        'replyToken' => $event['replyToken'],
                        'messages' => array(
                            array(
                                'type' => 'text',
                                'text' => 'あなたがいる場所は、' . $owm_data['name'] . "で天気予報は" . $owm_data['weather'][0]['main'] . "です。"
                            )
                        )
                   ));
                   break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
