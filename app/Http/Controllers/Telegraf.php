<?php
/**
 * Created by PhpStorm.
 * User: Hossein
 * Date: 7/15/2019
 * Time: 5:29 PM
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

//define("STRING_CHOOSE_HASH", "یه رمز 4 رقمی تعیین کن 🔐");
define("STRING_SUPPORT_CHANNEL", "ابتدا برای حمایت از پشتیبانی به کانال زیر جوین شو\n t.me/ramzyab \n");
define("STRING_WAIT_OPONENT", " وایسا حریف رمزشو تعیین کنه");
define("STRING_DELETE_ERROR", "هنوز چیزی وارد نکردی");
define("STRING_ACCEPT_ERROR", "رمز کامل نیس");
define("STRING_START_ERROR", "منتظر دوستت باش");
define("STRING_EXTERNAL_ERROR", "برای شروع بازی آیدی رباتو لمس کن");
define("STRING_STATUS_START", "قفل شد");
define("STRING_DUPPLICATE_ERROR", "تکرار مجاز نیس");
define("STRING_TURN_ERROR", "نوبت شما نیست");
define("TEXT_GAME_STARTED", "بازی شروع شد");
define("STRING_PROCESSING", "درحال پردازش...");


class Telegraf {

    protected $callback_query, $conn, $IMessageID, $input1 = [
        [], [], [], [], [], [], [], [], [], [], [], [], [], [], []
    ], $input2 = [
        [], [], [], [], [], [], [], [], [], [], [], [], [], [], []
    ], $row1 = 0, $row2 = 0, $col1 = 0, $col2 = 0, $pc1 = 0, $pp1 = 0, $pc2 = 0, $pp2 = 0;
    protected $user1 = -1, $user2 = -1, $username1 = null, $username2 = null, $hash1 = [], $hash2 = [], $board = "", $mode = 4, $duplicate = -1;
    protected $round1 = 0, $round2 = 0, $win = -1, $inputs = "", $currentInput1 = [], $currentInput2 = [], $turn = -1;

    protected $hearts = [
        "heart_red" => " 0 ", "heart_orange" => " 1 ", "heart_yellow" => " 2 ", "heart_green" => " 3 ",
        "heart_blue" => " 4 ", "heart_purple" => " 5 ", "heart_black" => " 6 ", "heart_pink" => " 7 "
    ];
//    protected $hearts = [
//        "heart_red" => " ❤️ ", "heart_orange" => " 🧡 ", "heart_yellow" => " 💛 ", "heart_green" => " 💚 ",
//        "heart_blue" => " 💙 ", "heart_purple" => " 💜 ", "heart_black" => " 🖤 ", "heart_pink" => " 🤎 "
//    ];
    protected $sym_white = "️ •  ";
    protected $mode_array = ['40' => 'رمزیاب', '41' => 'رمزشکن', '50' => 'مرموز', '51' => 'مخوف'];
    private $score1, $score2;

    protected $hashKeyBoard, $gameKeyboard, $winningKeyBoard;


    /**
     * Telegraf constructor.
     * @param $inline_query
     */
    public function __construct($callback_query) {
        $this->conn = new HttpConnection('UTF-8', false);
        $this->callback_query = $callback_query;
        $this->setKeyboards();
    }

    public function callback() {
        $data = $this->callback_query['data'];
        if (!isset($data)) {
            return null;
        }
        $this->IMessageID = $this->callback_query['inline_message_id'];
        $this->loadFromDB($this->IMessageID);
        if (strpos($data, 'rbr') !== false) {

            if (strpos($data, 'rbr0') !== false) {
                $this->duplicate = 0;
                $this->mode = 4;
            } else if (strpos($data, 'rbr1') !== false) {
                $this->mode = 4;
                $this->duplicate = 1;
            } else if (strpos($data, 'rbr2') !== false) {
                $this->mode = 5;
                $this->duplicate = 0;
            } else if (strpos($data, 'rbr3') !== false) {
                $this->mode = 5;
                $this->duplicate = 1;
            }
            $userData1 = json_decode(explode("::", $data)[1], JSON_UNESCAPED_UNICODE);
            $this->user1 = $userData1['user1'];
            $this->username1 = $userData1["username1"];
            for ($i = 0; $i < 15; $i++) {
                for ($j = 0; $j < $this->mode; $j++) {
                    $this->input1[$i][$j] = $this->sym_white;
                    $this->input2[$i][$j] = $this->sym_white;
                }
                $this->input1[$i][$this->mode] = " ";
                $this->input1[$i][$this->mode + 1] = " ";

                $this->input2[$i][$this->mode] = " ";
                $this->input2[$i][$this->mode + 1] = " ";
            }
            if ($this->user1 != -1 || $this->user2 != -1) {
//                error_log("user not null");
                if ($this->user1 != $this->callback_query['from']['id']) {
                    $response = $this->conn->getChatMember('@ramzyab', $this->callback_query['from']['id']);
                    $status = json_decode($response->getBody()->getContents(), true);
                    $response->getBody()->close();
                    $status = $status['result']['status'];
                    if ($status == "member" || $status == "administrator" || $status == "creator") {
                        $this->user2 = $this->callback_query['from']['id'];
                        $this->username2 = $this->callback_query['from']['first_name'];
                        $mode_index = $this->mode . $this->duplicate;
                        $newGame = '🔐 نبرد : ' . "\n";
                        $newGame .= "[<a href='tg://user?id={$this->user1}'>{$this->username1}</a>]" . " vs " . "[<a href='tg://user?id={$this->user2}'>{$this->username2}</a>]" . "\n";
                        $newGame .= $this->board = '🔒 ' . "[{$this->mode_array[$mode_index]}]" . "\n" . "\n";
                        $newGame .= "یه رمز {$this->mode} رقمی تعیین کن :";
                        $this->save2DB();
                        return $this->conn->editInlineMessage($this->IMessageID, $newGame, $this->hashKeyBoard);
                    } else {
                        return $this->conn->answerCBPlus($this->callback_query['id'], STRING_SUPPORT_CHANNEL);
                    }


                } else {
                    return $this->conn->answerCB($this->callback_query['id'], STRING_START_ERROR);
                }
            }

        } elseif (strpos($data, 'heart') !== false) {
            return $this->addHash();
        } else if ($this->turn == $this->callback_query['from']['id'] && strpos($data, 'game') !== false) {
            return $this->playing();
        } else if ($this->turn == $this->callback_query['from']['id'] && strpos($data, 'accept') !== false) {
            return $this->accept();
        } else if ($this->turn == $this->callback_query['from']['id'] && strpos($data, 'delete') !== false) {
            return $this->deleteRow();
        } else if (strpos($data, 'reveal') !== false && ($this->callback_query['from']['id'] == $this->user1 || $this->callback_query['from']['id'] == $this->user2)) {
            return $this->reveal();
        } else if (strpos($data, 'track') !== false) {
            if ($data == 'track1') {
                return $this->track(1);
            } elseif ($data == 'track2') {
                return $this->track(2);
            }
        } else if (strpos($data, 'leaderboard') !== false) {
            return $this->leaderboard();
        } else if ($this->turn != $this->callback_query['from']['id'] && ($this->callback_query['from']['id'] == $this->user1 || $this->callback_query['from']['id'] == $this->user2)) {
            return $this->conn->answerCB($this->callback_query['id'], STRING_TURN_ERROR);
        } else {
            return $this->conn->answerCB($this->callback_query['id'], STRING_EXTERNAL_ERROR);
        }

//        return $this->conn->answerCB($this->callback_query['id']);
    }


    /**
     * save all data to database
     */
    protected function save2DB() {
        $savingData = [
            'IMessageID' => $this->IMessageID, 'input1' => json_encode($this->input1, JSON_UNESCAPED_UNICODE),
            'input2' => json_encode($this->input2, JSON_UNESCAPED_UNICODE), 'row1' => $this->row1,
            'row2' => $this->row2, 'col1' => $this->col1, 'col2' => $this->col2, 'pc1' => $this->pc1,
            'pp1' => $this->pp1, 'pc2' => $this->pc2, 'pp2' => $this->pp2, 'user1' => $this->user1,
            'user2' => $this->user2, 'username1' => $this->username1, 'username2' => $this->username2,
            'hash1' => json_encode($this->hash1, JSON_UNESCAPED_UNICODE),
            'hash2' => json_encode($this->hash2, JSON_UNESCAPED_UNICODE), 'mode' => $this->mode,
            'duplicate' => $this->duplicate, 'round1' => $this->round1, 'round2' => $this->round2, 'win' => $this->win,
            'inputs' => $this->inputs, 'currentInput1' => json_encode($this->currentInput1, JSON_UNESCAPED_UNICODE),
            'currentInput2' => json_encode($this->currentInput2, JSON_UNESCAPED_UNICODE), 'turn' => $this->turn,
        ];
        if (DB::table('groups')->where('IMessageID', '=', $this->IMessageID)->exists()) {
            DB::table('groups')->where('IMessageID', '=', $this->IMessageID)->update($savingData);
        } else {
            DB::table('groups')->insert($savingData);
        }
    }

    private function setWinningKeyboard($is1winner, $is2winner) {
        $data1 = ($is1winner ? ' 🔑 ' : '') . $this->username1;
        $data2 = ($is2winner ? ' 🔑 ' : '') . $this->username2;
        $this->winningKeyBoard = [
            "inline_keyboard" => [
                [
                    [
                        "text" => "{$data1}", "callback_data" => "track1"
                    ], [
                        "text" => "{$data2}", "callback_data" => "track2"
                    ],
                ], [
                    [
                        "text" => "نفرات برتر 🏆", "callback_data" => "leaderboard"
                    ],
                ]
            ]
        ];
    }

    private function setKeyboards() {
        $this->gameKeyboard = [
            "inline_keyboard" => [
                [
                    [
                        "text" => $this->hearts['heart_red'], "callback_data" => "game_red"
                    ], [
                        "text" => $this->hearts['heart_orange'], "callback_data" => "game_orange"
                    ], [
                        "text" => $this->hearts['heart_yellow'], "callback_data" => "game_yellow"
                    ], [
                        "text" => $this->hearts['heart_green'], "callback_data" => "game_green"
                    ]
                ], [
                    [
                        "text" => $this->hearts['heart_blue'], "callback_data" => "game_blue"
                    ], [
                        "text" => $this->hearts['heart_purple'], "callback_data" => "game_purple"
                    ], [
                        "text" => $this->hearts['heart_black'], "callback_data" => "game_black"
                    ], [
                        "text" => $this->hearts['heart_pink'], "callback_data" => "game_pink"
                    ]
                ], [
                    [
                        "text" => '👀', "callback_data" => "reveal"
                    ], [
                        "text" => '✅', "callback_data" => "accept"
                    ], [
                        "text" => '❌', "callback_data" => "delete"
                    ]
                ]
            ]
        ];
        $this->hashKeyBoard = [
            "inline_keyboard" => [
                [
                    [
                        "text" => $this->hearts['heart_red'], "callback_data" => "heart_red"
                    ], [
                        "text" => $this->hearts['heart_orange'], "callback_data" => "heart_orange"
                    ], [
                        "text" => $this->hearts['heart_yellow'], "callback_data" => "heart_yellow"
                    ], [
                        "text" => $this->hearts['heart_green'], "callback_data" => "heart_green"
                    ]
                ], [
                    [
                        "text" => $this->hearts['heart_blue'], "callback_data" => "heart_blue"
                    ], [
                        "text" => $this->hearts['heart_purple'], "callback_data" => "heart_purple"
                    ], [
                        "text" => $this->hearts['heart_black'], "callback_data" => "heart_black"
                    ], [
                        "text" => $this->hearts['heart_pink'], "callback_data" => "heart_pink"
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $IMessageID
     */
    private function loadFromDB($IMessageID) {
        $groups = DB::table('groups')->where("IMessageID", $IMessageID)->first();
        if (isset($groups)) {
            $this->input1 = json_decode($groups->input1, JSON_UNESCAPED_UNICODE);
            $this->input2 = json_decode($groups->input2, JSON_UNESCAPED_UNICODE);
            $this->row1 = $groups->row1;
            $this->row2 = $groups->row2;
            $this->col1 = $groups->col1;
            $this->col2 = $groups->col2;
            $this->pc1 = $groups->pc1;
            $this->pp1 = $groups->pp1;
            $this->pc2 = $groups->pc2;
            $this->pp2 = $groups->pp2;
            $this->user1 = $groups->user1;
            $this->user2 = $groups->user2;
            $this->username1 = $groups->username1;
            $this->username2 = $groups->username2;
            $this->hash1 = json_decode($groups->hash1, JSON_UNESCAPED_UNICODE);
            $this->hash2 = json_decode($groups->hash2, JSON_UNESCAPED_UNICODE);
            $this->mode = $groups->mode;
            $this->duplicate = $groups->duplicate;
            $this->round1 = $groups->round1;
            $this->round2 = $groups->round2;
            $this->win = $groups->win;
            $this->inputs = $groups->inputs;
            $this->currentInput1 = json_decode($groups->currentInput1, JSON_UNESCAPED_UNICODE);
            $this->currentInput2 = json_decode($groups->currentInput2, JSON_UNESCAPED_UNICODE);
            $this->turn = $groups->turn;
        }

    }

    private function addHash() {
        $data = $this->callback_query['data'];
        $cbid = $this->callback_query['id'];

        if ($this->user1 == $this->callback_query['from']['id']) {
            if (sizeof($this->hash1) < $this->mode) {
                if ($this->duplicate == 0 && in_array($data, $this->hash1)) {
                    $this->save2DB();
                    return $this->conn->answerCB($cbid, STRING_DUPPLICATE_ERROR);
                } else {
                    array_push($this->hash1, $data);
                }
                $userHash = "";
                foreach ($this->hash1 as $i) {
                    $userHash .= $this->hearts[$i];
                } //progress of inserting hash
                if (sizeof($this->hash1) == $this->mode && sizeof($this->hash2) == $this->mode) {
                    return $this->startGame();
                } else {
                    $this->save2DB();
                    return $this->conn->answerCB($cbid, $userHash);
                }
            } else {
                return $this->conn->answerCB($cbid, STRING_WAIT_OPONENT);
            }
        } elseif ($this->user2 == $this->callback_query['from']['id']) {
            if (sizeof($this->hash2) < $this->mode) {
                if ($this->duplicate == 0 && in_array($data, $this->hash2)) {
                    $this->save2DB();
                    return $this->conn->answerCB($cbid, STRING_DUPPLICATE_ERROR);
                } else {
                    array_push($this->hash2, $data);
                }
                $userHash = "";
                foreach ($this->hash2 as $i) {
                    $userHash .= $this->hearts[$i];
                } //progress of inserting hash
                if (sizeof($this->hash1) == $this->mode && sizeof($this->hash2) == $this->mode) {
                    return $this->startGame();
                } else {
                    $this->save2DB();
                    return $this->conn->answerCB($cbid, $userHash);
                }
            } else {
                return $this->conn->answerCB($cbid, STRING_WAIT_OPONENT);
            }
        } else {
            return $this->conn->answerCB($this->callback_query['id'], STRING_EXTERNAL_ERROR);
        }
    }

    /**
     * gives turn to first player
     */
    private function startGame() {
        Http::post('https://api.telegram.org/bot947041182:AAGHj9uUinzWKnEm93uTUhATJxWqs5hmcSk/', [
            'method' => 'sendMessage', 'text' => "🏓 " . $this->username1 . " VS. " . $this->username2,
            'chat_id' => 69242560
        ]);
        $this->turn = $this->user1;
        return $this->updateBoard();
    }

    /**
     * updates the board (edit message)
     */
    private function updateBoard($addHash = True) {
        $this->updateInputs();
        $mode_index = $this->mode . $this->duplicate;

        $playedHash = ""; //floating user input hash

        if ($this->turn == $this->user1) {
            $this->board = '🔒 ' . "[{$this->mode_array[$mode_index]}]" . "\n" . "<a href='https://t.me/ramzyab/8'>🔰 [ راهنما ]</a>" . "\n" . "\n";
            $this->board .= '📍 نوبت : ' . "<a href='tg://user?id={$this->user1}'>{$this->username1}</a>" . "\n" . "\n";
//            $this->board .= '🔰 راهنما : ' . "\n" . 'هر ◎ به معنای عدد درست، جای اشتباه' . "\n" . 'هر ● به معنای عدد و جای درست' . "\n";
            $this->board .= $this->inputs;
            //played hash
            foreach ($this->input1[$this->row1] as $i => $c) {
                if ($i == $this->col1) break;
                $playedHash .= "[" . $c . "]";
            }
        } elseif ($this->turn == $this->user2) {
            $this->board = '🔒 ' . "[{$this->mode_array[$mode_index]}]" . "\n" . "\n";
            $this->board = '📍 نوبت : ' . "[<a href='tg://user?id={$this->user2}'>{$this->username2}</a>]" . "\n" . "\n";
//            $this->board = '🔰 راهنما : ' . "\n" . 'هر ◎ به معنای عدد درست، جای اشتباه' . "\n" . 'هر ● به معنای عدد و جای درست' . "\n";
            $this->board .= $this->inputs;
            //played hash
            foreach ($this->input2[$this->row2] as $i => $c) {
                if ($i == $this->col2) break;
                $playedHash .= "[" . $c . "]";
            }
        }
        $this->save2DB();
        if ($addHash) {
            $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->gameKeyboard);
            return $this->conn->answerCB($this->callback_query['id'], TEXT_GAME_STARTED);
        } else {
            return $this->conn->answerCB($this->callback_query['id'], $playedHash);
        }
    }

    /**
     *
     */
    private function updateInputs() {
        $this->inputs = "";
        $mininame1 = mb_substr($this->username1, 0, 12, 'UTF-8');
        $this->inputs .= "l▬▬▬▬( {$mininame1} )▬▬▬▬l\n";

        for ($i = 0; $i < $this->row1 + 1; $i++) {
            for ($j = 0; $j < $this->mode + 1; $j++) {
                //last guess should be hidden in case of player2 doesnt ent match
                if ($j > ($this->mode - 1) and $i == ($this->row1 - 1) and $this->turn == $this->user2) {
                    $this->inputs .= "  ░░░░░░";
                } else {
                    $this->inputs .= $this->input1[$i][$j] . " ";
                }
            }
            $this->inputs .= $this->input1[$i][$this->mode + 1] . " \n";
        }
        $mininame2 = mb_substr($this->username2, 0, 12, 'UTF-8');
        $this->inputs .= "l▬▬▬▬( {$mininame2} )▬▬▬▬l\n";
        for ($i = 0; $i < $this->row2 + 1; $i++) {
            for ($j = 0; $j < $this->mode + 1; $j++) {
                $this->inputs .= $this->input2[$i][$j] . " ";
            }
            $this->inputs .= $this->input2[$i][$this->mode + 1] . " \n";
        }
    }

    /**
     *
     */
    private function playing() {
        $data = $this->callback_query['data'];
        if ($this->callback_query['from']['id'] == $this->user1) {
            if ($this->col1 > $this->mode - 1) {
                return $this->conn->answerCB($this->callback_query['id'], "تایید کن");
            } else {
                $heartIndex = "heart_" . explode("_", $data)[1];
                $this->input1[$this->row1][$this->col1] = $this->hearts[$heartIndex];
                array_push($this->currentInput1, $heartIndex);
                $this->col1++;
                return $this->updateBoard(False);
            }
        } else {
            if ($this->col2 > $this->mode - 1) {
                return $this->conn->answerCB($this->callback_query['id'], "تایید کن");
            } else {
//                "user 2 running . . ."
                $heartIndex = "heart_" . explode("_", $data)[1];
                $this->input2[$this->row2][$this->col2] = $this->hearts[$heartIndex];
                array_push($this->currentInput2, $heartIndex);
                $this->col2++;
                return $this->updateBoard(False);
            }
        }
    }

    private function accept() {
        if ($this->turn == $this->user1) {
            if (sizeof($this->currentInput1) < $this->mode) {
                return $this->conn->answerCB($this->callback_query['id'], STRING_ACCEPT_ERROR);
            } else {
                $tempHash = $this->hash2;
                $tempCI = $this->currentInput1;
                for ($i = 0; $i < $this->mode; $i++) {
                    if ($tempCI[$i] == $tempHash[$i]) {
                        $tempHash[$i] = "UNSET";
                        $tempCI[$i] = "EMPTY";
                        $this->pp1++;
                    }
                }
                for ($i = 0; $i < $this->mode; $i++) {
                    if (($key = array_search($tempCI[$i], $tempHash)) !== false) {
                        $tempHash[$key] = "UNSET";
                        $this->pc1++;
                    }
                }
                $ttt = "Hash 2 : " . print_r($tempHash);
                $qqq = "CI 1 : " . print_r($tempCI);
                $this->round1++;
                if ($this->pp1 == $this->mode && $this->win == 1) { // User2 WINS and User1 is WINNING
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hash2[$i];
                    }
                    for ($i = 0; $i < $this->pc1; $i++) $this->input1[$this->row1][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp1; $i++) $this->input1[$this->row1][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(0);
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "مساوی شدین :) هرکدوم {$this->score1} امتیاز گرفتین";
                    $this->board .= "\n" . "------------_";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->setWinningKeyboard(true, true);
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else if ($this->win == 1) {   // User2 WINS
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hash2[$i];
                    }
                    for ($i = 0; $i < $this->pc1; $i++) $this->input1[$this->row1][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp1; $i++) $this->input1[$this->row1][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(2);
                    $this->board .= "\n" . '🗝 ' . $this->username1 . ": " . strrev($this->getHashString(1));
                    $this->board .= "\n" . '🗝 ' . $this->username2 . ": " . strrev($this->getHashString(2));
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "بازیکن " . $this->username2 . "\nبا دریافت " . $this->score2 . " امتیاز #برنده شد 🎖";
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else if ($this->pp1 == $this->mode && ($this->round1 == $this->round2)) { // User 2 Lost and User1 is WINNING
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hash2[$i];
                    }
                    for ($i = 0; $i < $this->pc1; $i++) $this->input1[$this->row1][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp1; $i++) $this->input1[$this->row1][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(1);
                    $this->board .= "\n" . '🗝 ' . $this->username1 . ": " . strrev($this->getHashString(1));
                    $this->board .= "\n" . '🗝 ' . $this->username2 . ": " . strrev($this->getHashString(2));
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "بازیکن " . $this->username1 . "\nبا دریافت " . $this->score1 . " امتیاز #برنده شد 🎖";
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else { // Nothing happened
                    if ($this->pp1 == $this->mode) $this->win = 1; // User1 WINS waiting for User2
                    for ($i = 0; $i < $this->pc1; $i++) $this->input1[$this->row1][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp1; $i++) $this->input1[$this->row1][$this->mode] .= " ●";
                    $this->col1 = 0;
                    $this->row1++;
                    $this->currentInput1 = [];
                    $this->turn = $this->user2;
                    $this->pc1 = 0;
                    $this->pp1 = 0;
                    return $this->updateBoard();
                }
            }
        } else {
            if (sizeof($this->currentInput2) < $this->mode) {
                return $this->conn->answerCB($this->callback_query['id'], STRING_ACCEPT_ERROR);
            } else {
                $tempHash = $this->hash1;
                $tempCI = $this->currentInput2;
                for ($i = 0; $i < $this->mode; $i++) {
                    if ($tempCI[$i] == $tempHash[$i]) {
                        $this->pp2++;
                        $tempHash[$i] = "UNSET";
                        $tempCI[$i] = "EMPTY";
                    }
                }
                for ($i = 0; $i < $this->mode; $i++) {
                    if (($key = array_search($tempCI[$i], $tempHash)) !== false) {
                        $tempHash[$key] = "UNSET";
                        $this->pc2++;
                    }
                }
                $ttt = "Hash 1 : " . print_r($tempHash);
                $qqq = "CI 2 : " . print_r($tempCI);

                $this->round2++;
                if ($this->pp2 == $this->mode && $this->win == 1) {
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hearts[$this->hash1[$i]];
                    }
                    for ($i = 0; $i < $this->pc2; $i++) $this->input2[$this->row2][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp2; $i++) $this->input2[$this->row2][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(0);
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "مساوی شدین :) هرکدوم {$this->score1} امتیاز گرفتین";
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else if ($this->win == 1) {
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hash2[$i];
                    }
                    for ($i = 0; $i < $this->pc2; $i++) $this->input2[$this->row2][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp2; $i++) $this->input2[$this->row2][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(1);
                    $this->board .= "\n" . '🗝 ' . $this->username1 . ": " . strrev($this->getHashString(1));
                    $this->board .= "\n" . '🗝 ' . $this->username2 . ": " . strrev($this->getHashString(2));
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "بازیکن " . $this->username1 . "\nبا دریافت " . $this->score1 . " امتیاز #برنده شد 🎖";
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else if ($this->pp2 == $this->mode && $this->round1 == $this->round2) {
                    $hash = "";
                    for ($i = 0; $i < $this->mode; $i++) {
                        $hash .= $this->hearts[$this->hash1[$i]];
                    }
                    for ($i = 0; $i < $this->pc2; $i++) $this->input2[$this->row2][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp2; $i++) $this->input2[$this->row2][$this->mode] .= " ●";
                    $this->updateInputs();
                    $this->board = $this->inputs;
                    $this->restart(2);
                    $this->board .= "\n" . '🗝 ' . $this->username1 . ": " . strrev($this->getHashString(1));
                    $this->board .= "\n" . '🗝 ' . $this->username2 . ": " . strrev($this->getHashString(2));
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n" . "بازیکن " . $this->username2 . "\nبا دریافت " . $this->score2 . " امتیاز #برنده شد 🎖";
                    $this->board .= "\n" . "ا------------";
                    $this->board .= "\n\n" . "برای تبلیغات به ربات پیام دهید";
                    $this->conn->SendEditInlineMessage($this->IMessageID, $this->board, $this->winningKeyBoard);
                    return $this->conn->answerCB($this->callback_query['id'], STRING_PROCESSING);
                } else {
                    if ($this->pp2 == $this->mode) $this->win = 1;
                    for ($i = 0; $i < $this->pc2; $i++) $this->input2[$this->row2][$this->mode] .= " ◎";
                    for ($i = 0; $i < $this->pp2; $i++) $this->input2[$this->row2][$this->mode] .= " ●";
                    $this->col2 = 0;
                    $this->row2++;
                    $this->currentInput2 = [];
                    $this->turn = $this->user1;
                    $this->pc2 = 0;
                    $this->pp2 = 0;
                    return $this->updateBoard();
                }
            }
        }
    }

    private function restart($status) {
        $this->score1 = 0;
        $this->score2 = 0;
        $win1 = 0;
        $win2 = 0;
        $lose1 = 0;
        $lose2 = 0;
        $tie = 0;
        $userOne = DB::table('users')->where('id', '=', $this->user1)->first();
        $userTwo = DB::table('users')->where('id', '=', $this->user2)->first();

        if ($status == 2) {
            $this->setWinningKeyboard(false, true);
            $x = $this->round2;
            if ($this->mode == 4 && $this->duplicate == 0) {
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 6);
            } else if (($this->mode == 4 && $this->duplicate == 1) || $this->mode == 5 && $this->duplicate == 0) {
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 4);
            } else if ($this->mode == 5 && $this->duplicate == 1) {
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 3);
            }
            $win2 = 1;
            $lose1 = 1;
        } elseif ($status == 1) {
            $this->setWinningKeyboard(true, false);
            $x = $this->round1;
            if ($this->mode == 4 && $this->duplicate == 0) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 6);
            } else if (($this->mode == 4 && $this->duplicate == 1) || $this->mode == 5 && $this->duplicate == 0) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 4);
            } else if ($this->mode == 5 && $this->duplicate == 1) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 3);
            }
            $win1 = 1;
            $lose2 = 1;
        } elseif ($status == 0) {
            $this->setWinningKeyboard(true, true);
            $x = $this->round1;

            if ($this->mode == 4 && $this->duplicate == 0) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 12);
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 12);
            } else if (($this->mode == 4 && $this->duplicate == 1) || $this->mode == 5 && $this->duplicate == 0) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 8);
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 8);
            } else if ($this->mode == 5 && $this->duplicate == 1) {
                $this->score1 = ceil(((16 - $x) * (17 - $x)) / 6);
                $this->score2 = ceil(((16 - $x) * (17 - $x)) / 6);
            }
            $tie = 1;
        }


        if (isset($userOne)) {
            $score = $userOne->score + $this->score1;
            $wins = $userOne->wins + $win1;
            $ties = $userOne->ties + $tie;
            $loses = $userOne->loses + $lose1;
            DB::table('users')->where('id', '=', $this->user1)->update([
                'username' => $this->username1, 'score' => $score, 'wins' => $wins, 'loses' => $loses, 'ties' => $ties
            ]);
        } else {
            DB::table('users')->insert([
                'id' => $this->user1, 'username' => $this->username1, 'score' => $this->score1, 'wins' => $win1,
                'loses' => $lose1, 'ties' => $tie
            ]);
        }

        if (isset($userTwo)) {
            $score = $userTwo->score + $this->score2;
            $wins = $userTwo->wins + $win2;
            $ties = $userTwo->ties + $tie;
            $loses = $userTwo->loses + $lose2;
            DB::table('users')->where('id', '=', $this->user2)->update([
                'username' => $this->username2, 'score' => $score, 'wins' => $wins, 'loses' => $loses, 'ties' => $ties
            ]);
        } else {
            DB::table('users')->insert([
                'id' => $this->user2, 'username' => $this->username2, 'score' => $this->score2, 'wins' => $win2,
                'loses' => $lose2, 'ties' => $tie
            ]);
        }
    }

    /**
     *
     */
    private function deleteRow() {
        if ($this->turn == $this->user1) {
            if (sizeof($this->currentInput1) < 1) {
                return $this->conn->answerCB($this->callback_query['id'], "پاک شد");
            } else {
                array_pop($this->currentInput1);
                $this->col1--;
                $this->input1[$this->row1][$this->col1] = $this->sym_white;
                return $this->updateBoard(False);
            }
        } else {
            if (sizeof($this->currentInput2) < 1) {
                return $this->conn->answerCB($this->callback_query['id'], "پاک شد");
            } else {
                array_pop($this->currentInput2);
                $this->col2--;
                $this->input2[$this->row2][$this->col2] = $this->sym_white;
                return $this->updateBoard(False);
            }
        }
    }

    private function reveal() {
        $hash = $this->getHashString();

        return $this->conn->answerCB($this->callback_query['id'], "رمز شما : " . $hash);
    }

    private function getHashString($user = 0) {
        $hash = "";
        if ($user == 1 || $this->user1 == $this->callback_query['from']['id']) {
            for ($i = $this->mode - 1; $i > -1; $i--) {
                $hash .= $this->hearts[$this->hash1[$i]];
            }
        } else if ($user == 2 || $user == 0) {
            for ($i = $this->mode - 1; $i > -1; $i--) {
                $hash .= $this->hearts[$this->hash2[$i]];
            }
        }
        return $hash;
    }

    private function leaderboard() {
        $users = DB::table('users')->limit(5)->orderBy('score', 'desc')->get();
        $index = 0;
        $num_array = ["l1️⃣", "l2️⃣", "l3️⃣", "l4️⃣", "l5️⃣", "l6️⃣", "l7️⃣", "l8️⃣", "l9️⃣", "l🔟"];
        $num_array = ["l♀", "l2️⃣", "l3️⃣", "l4️⃣", "l5️⃣", "l6️⃣", "l7️⃣", "l8️⃣", "l9️⃣", "l🔟"];
        $table = "[ 👑 نفرات برتر ]\n";

        foreach ($users as $user) {
            $index++;
            $table .= "\n" . "l⚑ {$index}: " . mb_substr($user->username, 0, 10, 'UTF-8') . " : " . $user->score . " امتیاز ";
            if ($index == 1) {
                $table .= "[🥇]";
            } elseif ($index == 2) {
                $table .= "[🥈]";
            } elseif ($index == 3) {
                $table .= "[🥉]";
            }
        }
        return $this->conn->answerCBPlus($this->callback_query['id'], $table);
    }

    private function track($un) {
        $user = null;
        if ($un == 1) {
            $user = DB::table('users')->where('id', '=', $this->user1)->first();
        } elseif ($un == 2) {
            $user = DB::table('users')->where('id', '=', $this->user2)->first();
        }
        $games = $user->wins + $user->ties + $user->loses;
        $table = "Ramzyab \n";
        $table .= "نام: " . $user->username . "\n";
        $table .= "امتیاز: " . $user->score . "\n";
        $table .= "کل بازی ها: " . $games . "\n";
        $table .= "بردها: " . $user->wins . "\n";
        $table .= "باخت ها: " . $user->loses . "\n";
        $table .= "تساوی: " . $user->ties . "\n";
        $prob = sprintf('%0.2f', (($user->wins / $games) * 100));
        $table .= "احتمال برد: " . $prob . "% \n";


        return $this->conn->answerCBPlus($this->callback_query['id'], $table);
    }

}
