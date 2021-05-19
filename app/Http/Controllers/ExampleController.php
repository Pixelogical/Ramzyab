<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\HttpConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExampleController extends Controller {

    protected $conn;
    protected $hearts = [
        "heart_red" => " 0 ", "heart_orange" => " 1 ", "heart_yellow" => " 2 ", "heart_green" => " 3 ",
        "heart_blue" => " 4 ", "heart_purple" => " 5 ", "heart_black" => " 6 ", "heart_pink" => " 7 "
    ];


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
//        $this->conn = new  HttpConnection('UTF-8', false, ['base_uri' => 'https://webhook.site']);
        $this->conn = new HttpConnection('UTF-8', false);
    }


    public function main() {
        return 'youre in rmzyabbot main func';


    }

    public function setWebhook() {
        $response =  Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8/', [
            'method' => 'setWebhook', 'url' => "https://freelancer-project.ir/ramzyabbot/saeed"
        ]);
        return $response;
    }

//    public function getUpdates(Request $request){
//        $update = json_decode($request->getContent(), JSON_UNESCAPED_UNICODE);
//        if (array_key_exists("callback_query", $update)) {
//            $telegraf = new Telegraf($update['callback_query']);
//            $telegraf->callback();
//        }
//    }

    public function getUpdates(Request $request) {

        $update = json_decode($request->getContent(), JSON_UNESCAPED_UNICODE);
        if (array_key_exists("callback_query", $update)) {
            $telegraf = new Telegraf($update['callback_query']);
            return $telegraf->callback();
        } elseif (array_key_exists("inline_query", $update)) {

            $user1 = $update['inline_query']['from']['id'];
            $username1 = $update['inline_query']['from']['first_name'];
            //ajagh vajagh fonts should be chopped.
            if (strlen($username1) > 18) {
                while (strlen($username1) > 18) {
                    $username1 = mb_substr($username1, 0, -1, 'UTF-8');
                }
                $username1 .= "...";
            }
            $userData1 = ['user1' => $user1, 'username1' => $username1];
            $results = [
                [
                    "type" => 'article', "id" => "759150",
                    "thumb_url" => "https://dl.dropboxusercontent.com/s/vf1pcgm92h0hefl/ramzyab.png",
                    "photo_width" => 20, "photo_height" => 20, "title" => "رمزیاب (آسان)",
                    "description" => "4 تایی - تکرار غیر مجاز \nحد اکثر امتیاز 40 - 15 فرصت",
                    "parse_mode" => "Markdown", "input_message_content" => [
                    "message_text" => '🔐 بیا با هم رمزیاب بازی کنیم :' . "\n" . '🔍 هر کی زودتر رمزو پیدا کنه برنده است!' . "\n" . "\n" . '🔒 حالت بازی : [رمزیاب]' . "\n" . " <a href='https://dl.dropboxusercontent.com/s/n48b8sx0b3lres3/ramzyabbot.jpg'>  </a>",
                    "parse_mode" => "HTML"
                ], "reply_markup" => [
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "باشه، بزن بریم",
                                "callback_data" => "rbr0::" . json_encode($userData1, JSON_UNESCAPED_UNICODE)
                            ], [
                                "text" => "کانال اسپانسر", "url" => "https://t.me/ramzyab",
                            ]
                        ]
                    ]
                ]
                ], [
                    "type" => 'article', "id" => "759151",
                    "thumb_url" => "https://dl.dropboxusercontent.com/s/dzy8o442ued4qeq/ramzshekan.png",
                    "photo_width" => 20, "photo_height" => 20, "title" => "رمزشکن (متوسط)",
                    "description" => "4 تایی - تکرار مجاز \nحد اکثر امتیاز 60 - 15 فرصت", "parse_mode" => "Markdown",
                    "input_message_content" => [
                        "message_text" => '🔐 بیا با هم رمزیاب بازی کنیم :' . "\n" . '🔍 هر کی زودتر رمزو پیدا کنه برنده است!' . "\n" . "\n" . '🔒 حالت بازی : [رمزشکن]' . "\n" . " <a href='https://dl.dropboxusercontent.com/s/n48b8sx0b3lres3/ramzyabbot.jpg'>  </a>",
                        "parse_mode" => "HTML"
                    ], "reply_markup" => [
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "باشه، بزن بریم",
                                    "callback_data" => "rbr1::" . json_encode($userData1, JSON_UNESCAPED_UNICODE)
                                ], [
                                    "text" => "کانال اسپانسر", "url" => "https://t.me/ramzyab",
                                ]
                            ]
                        ]
                    ]
                ], [
                    "type" => 'article', "id" => "759152",
                    "thumb_url" => "https://dl.dropboxusercontent.com/s/0v6w6n7p1hbauyi/marmooz.png",
                    "photo_width" => 20, "photo_height" => 20, "title" => "مرموز (متوسط)",
                    "description" => "5 تایی - تکرار غیر مجاز \nحد اکثر امتیاز 60 - 15 فرصت",
                    "parse_mode" => "Markdown", "input_message_content" => [
                        "message_text" => '🔐 بیا با هم رمزیاب بازی کنیم :' . "\n" . '🔍 هر کی زودتر رمزو پیدا کنه برنده است!' . "\n" . "\n" . '🔒 حالت بازی : [مرموز]' . "\n" . " <a href='https://dl.dropboxusercontent.com/s/n48b8sx0b3lres3/ramzyabbot.jpg'>  </a>",
                        "parse_mode" => "HTML"
                    ], "reply_markup" => [
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "باشه، بزن بریم",
                                    "callback_data" => "rbr2::" . json_encode($userData1, JSON_UNESCAPED_UNICODE)
                                ], [
                                    "text" => "کانال اسپانسر", "url" => "https://t.me/ramzyab",
                                ]
                            ]
                        ]
                    ]
                ], [
                    "type" => 'article', "id" => "759153",
                    "thumb_url" => "https://dl.dropboxusercontent.com/s/lkugt6rsp5lb9b6/makhoof.png",
                    "photo_width" => 20, "photo_height" => 20, "title" => "مخوف (سخت)",
                    "description" => "5 تایی - تکرار مجاز \nحد اکثر امتیاز 80 - 15 فرصت", "parse_mode" => "Markdown",
                    "input_message_content" => [
                        "message_text" => '🔐 بیا با هم رمزیاب بازی کنیم :' . "\n" . '🔍 هر کی زودتر رمزو پیدا کنه برنده است!' . "\n" . "\n" . '🔒 حالت بازی : [مخوف]' . "\n" . " <a href='https://dl.dropboxusercontent.com/s/n48b8sx0b3lres3/ramzyabbot.jpg'>  </a>",
                        "parse_mode" => "HTML"
                    ], "reply_markup" => [
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "باشه، بزن بریم",
                                    "callback_data" => "rbr3::" . json_encode($userData1, JSON_UNESCAPED_UNICODE)
                                ], [
                                    "text" => "کانال اسپانسر", "url" => "https://t.me/ramzyab",
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            return $this->sendAnswerInlineQuery($update['inline_query']['id'], $results);

        } else if (isset($update['message']) && isset($update['message']['text'])) {
            Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8', [
                'method' => 'sendMessage', 'text' => "مرسی. دریافت شد.", 'chat_id' => $update['message']['from']['id']
            ]);
            Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8', [
                'method' => 'sendMessage',
                'text' => $update['message']['from']['first_name'] . ": " . $update['message']['text'],
                'chat_id' => 69242560
            ]);
        }
//        elseif (array_key_exists("message", $update)) {
//            $message = $update['message'];
////            $this->sendMessage($message['chat']['id'], "Memory usage : " . memory_get_usage());
//            $this->sendMessage($message['chat']['id'], $update['message']['from']['first_name'] . '•');
//            DB::table('users')->insert([
//                'id' => rand(1, 100000), 'username' => $update['message']['from']['first_name'], 'score' => 42,
//                'wins' => 42, 'loses' => 42, 'ties' => 42
//            ]);
//
//        } elseif (array_key_exists("callback_query", $update)) {
//
//        }
//        error_log(print_r($request->input(), true));
        gc_collect_cycles();
        return 'ok';
    }

    public function getWebhookInfo() {
        dd($this->conn->sendRequest(null, 'getWebhookInfo'));
    }


    /**
     * @param $chatID
     * @param string $text
     */
    public function sendMessage($chatID, $text) {
        $this->conn->sendRequest([
            'chat_id' => $chatID, 'text' => $text, 'parse_mode' => "markdown"
        ], 'sendMessage');
    }

    /**
     * @param $inline_id
     * @param $results
     */
    public function sendAnswerInlineQuery($inline_id, $results) {
//        Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8', [
//            'method' => 'sendMessage', 'text' => "sending inline query", 'chat_id' => 69242560
//        ]);

//        $result = Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8', [
//            'method' => 'answerInlineQuery', 'inline_query_id' => $inline_id,
//            'results' => json_encode($results, JSON_UNESCAPED_UNICODE), 'cache_time' => 0,
//        ]);

//        Http::post('https://api.telegram.org/bot947041182:AAHJPHaUzE3NNMLy89_fbT5XPIY_BYvPdd8', [
//            'method' => 'sendMessage', 'text' => "result : " . $result, 'chat_id' => 69242560
//        ]);

        return $this->conn->sendRequest([
            'inline_query_id' => $inline_id, 'results' => json_encode($results, JSON_UNESCAPED_UNICODE),
            'cache_time' => 0,
        ], 'answerInlineQuery');

    }

    /**
     * @param string integer $inline_message_id
     * @param string $text
     * @param string $reply_markup
     */
    public function editInlineMessage($inline_message_id, $text, $reply_markup) {
        $this->conn->sendRequest([
            'inline_message_id' => $inline_message_id, 'text' => $text,
            'reply_markup' => "" . json_encode($reply_markup, JSON_UNESCAPED_UNICODE),
        ], 'editMessageText');

    }

    public function test() {
        // Memory usage: 4.55 GiB / 23.91 GiB (19.013557664178%)
//        $memUsage = $this->getServerMemoryUsage(false);
//        echo sprintf("Memory usage: %s / %s (%s%%)", $this->getNiceFileSize($memUsage["total"] - $memUsage["free"]), $this->getNiceFileSize($memUsage["total"]), $this->getServerMemoryUsage(true));
//        return ("Memory : " . memory_get_usage());
//        $savingData = [
//            'IMessageID' => rand(10, 654654), 'username1' => "Ϝ@☈♗.◗༄",
//            'username2' => "Ϝ@☈♗.◗༄", 'board' => "saasa",
//        ];
//        DB::table('groups')->insert($savingData);
//        error_log("77777777777777777777 Ϝ@☈♗.◗༄ 77777777777777777777777");
        return ("Ϝ@☈♗.◗༄            :" . utf8_decode("ÙÙØ¨Øª Ø¨Ø§Ø²ÛÚ©Ù Ï@ââ.âà¼ Ø§Ø³Øª
 Ø¢ÙÙØ²Ø´ Ú©ÙØªØ§ÙÙ..
 lâ¬â¬â¬â¬( Ï@ââ.â )â¬â¬â¬â¬l
ï¸ â¢   ï¸ â¢   ï¸ â¢   ï¸ â¢
lâ¬â¬â¬â¬( Pixel â¢ )â¬â¬â¬â¬l
ï¸ â¢   ï¸ â¢   ï¸ â¢   ï¸ â¢
"));
    }

    /**
     * @param $id
     * @param null $text
     */
    public function answerCB($id, $text = null) {
        if (isset($text)) {
            $this->conn->sendRequest([
                'callback_query_id' => $id, 'text' => $text
            ], 'answerCallbackQuery');
        } else {
            $this->conn->sendRequest([
                'callback_query_id' => $id
            ], 'answerCallbackQuery');
        }
    }
}
