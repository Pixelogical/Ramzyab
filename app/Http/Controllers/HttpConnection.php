<?php

/*
 * Copyright (c) 2019 Hossein Sekhavaty <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;

class HttpConnection {
    protected $endpoint;
    protected $baseURI = ['base_uri' => 'https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/'];
    protected $synchronous;
    /** @var Client */
    protected $client;


    /**
     * @param string $encoding
     * @param bool $synchronous
     */
    public function __construct($encoding, $synchronous = false) {
        $this->synchronous = $synchronous;
        $this->encoding = $encoding;
        $this->client = new Client($this->baseURI);
    }

    public function sendSyncRequest($jsonData, $endpoint) {
        $request = new Request('POST', $endpoint, ($jsonData ? ['query' => http_build_query($jsonData)] : []));
        $response = $this->client->send($request, ($jsonData ? [
            'timeout' => 5, 'query' => http_build_query($jsonData)
        ] : ['timeout' => 5]));
        return $response;
    }

    public function sendRequest($jsonData, $endpoint) {
//        Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
//            'method' => 'sendMessage', 'text' => "im in sendrequest", 'chat_id' => 69242560
//        ]);
        return response()->json(array_merge($jsonData, ['method' => $endpoint]));
//        Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
//            'method' => 'sendMessage', 'text' => "res : " . print_r($r, true), 'chat_id' => 69242560
//        ]);
//
//        return $r;

//        try {
//            $request = new Request('POST', $endpoint, ($jsonData ? ['query' => http_build_query($jsonData)] : []));
//            if (!$this->synchronous) {
//                $response = $this->client->sendAsync($request, ($jsonData ? ['query' => http_build_query($jsonData)] : []))->then(function (ResponseInterface $res) {
//                    error_log("CODE : " . $res->getStatusCode() . "\n");
//                    $body = $res->getBody();
//                    error_log("response : " . (string)$body . "\n");
//                    return (string)$body;
//                }, function (RequestException $e) {
//                    error_log("error1 : " . $e->getMessage() . "\n");
////                    $this->sendMessage("69242560", substr($e->getMessage(), 20, 50));
//                    error_log("error2 : " . $e->getRequest()->getMethod());
//                    return $e->getMessage();
////                    return 'failure';
//                })->wait(true);
//                return print_r($response, true);
//            }
//        } catch (ServerException $ex) {
//            $response = $ex->getResponse();
//            error_log($response);
//        } catch (ClientException $ex) {
//            $response = $ex->getResponse();
//            error_log($response);
//        }
//        return "omg";
    }

    /**
     * @return mixed
     */
    public function setWebhook() {
        return $this->sendRequest([
            'url' => 'https://freelancer-project.ir/ramzyabbot/saeed',
        ], 'setWebhook');
    }

    /**
     * @param $chatID
     * @param string $text
     */
    public function sendMessage($chatID, $text) {
        $this->sendRequest([
            'chat_id' => $chatID, 'text' => $text, 'parse_mode' => "markdown"
        ], 'sendMessage');
    }

    /**
     * @param $id
     * @param null $text
     */
    public function answerCB($id, $text = null) {
//        Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
//            'method' => 'sendMessage', 'text' => "ANSWERCB " . print_r($text, true), 'chat_id' => 69242560
//        ]);
        if (isset($text)) {
            return $this->sendRequest([
                'callback_query_id' => $id, 'text' => $text
            ], 'answerCallbackQuery');
        } else {
            return $this->sendRequest([
                'callback_query_id' => $id
            ], 'answerCallbackQuery');
        }
    }

    public function answerCBPlus($id, $text) {
        return $this->sendRequest([
            'callback_query_id' => $id, 'text' => $text, 'show_alert' => true, 'cache_time' => '20'
        ], 'answerCallbackQuery');
    }

    /**
     * @param string integer $inline_message_id
     * @param string $text
     * @param string $reply_markup
     */
    public function editInlineMessage($inline_message_id, $text, $reply_markup = null) {
        if (isset($reply_markup)) {
            return $this->sendRequest([
                'inline_message_id' => $inline_message_id, 'text' => $text,
                'reply_markup' => "" . json_encode($reply_markup, JSON_UNESCAPED_UNICODE), 'parse_mode' => 'HTML'
            ], 'editMessageText');
        } else {
            return $this->sendRequest([
                'inline_message_id' => $inline_message_id, 'text' => $text, 'parse_mode' => 'HTML'
            ], 'editMessageText');
        }
    }

    public function SendEditInlineMessage($inline_message_id, $text, $reply_markup = null) {
        if (isset($reply_markup)) {
            Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
                'method' => 'editMessageText', 'inline_message_id' => $inline_message_id, 'text' => $text,
                'reply_markup' => "" . json_encode($reply_markup, JSON_UNESCAPED_UNICODE), 'parse_mode' => 'HTML'
            ]);

        } else {
            Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
                'method' => 'editMessageText', 'inline_message_id' => $inline_message_id, 'text' => $text,
                'parse_mode' => 'HTML'
            ]);
        }

    }

    public function getChatMember($chat, $user) {
        return Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
            'method' => 'getChatMember', 'chat_id' => $chat, 'user_id' => $user
        ]);
    }

}
