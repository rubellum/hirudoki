<?php

namespace Hirudoki\Subscriber;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;

class Location
{
    protected $bot;
    protected $event;

    public function __construct(LINEBot $bot, LocationMessage $event)
    {
        $this->bot = $bot;
        $this->event = $event;
    }

    public function execute()
    {
        $event = $this->event;
        $bot = $this->bot;

        $userId = $event->getUserId();
        $replyToken = $event->getReplyToken();

        // ==============================
        // 位置情報を更新
        // ==============================

        $latitude = $event->getLatitude();
        $longitude = $event->getLongitude();

        $user_location = \ORM::for_table('user_location')->use_id_column('user_id')->find_one($userId);

        if (!$user_location) {
            $user_location = \ORM::for_table('user_location')->create();
            $user_location->user_id = $userId;
        }

        $user_location->latitude = $latitude;
        $user_location->longitude = $longitude;
        $user_location->created_at = date('Y-m-d H:i:s');

        $result = $user_location->save();

        if (!$result) {
            log_output(['error' => "user({$userId}) cannot create location"], __LINE__);
        }

        $user_event = \ORM::for_table('user_events')->create();
        $user_event->user_id = $userId;
        $user_event->type = 'update_location';
        $user_event->data = json_encode(['latitude' => $latitude, 'longitude' => $longitude]);
        $user_event->created_at = date('Y-m-d H:i:s');

        $result = $user_event->save();

        if (!$result) {
            log_output(['error' => "user({$userId}) cannot create location"], __LINE__);
        }

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('現在地を登録しました。次に検索したいメッセージを送信してください。下部のリッチメニューからショートカット検索もできます。');
        $response = $bot->replyMessage($replyToken, $textMessageBuilder);

        if (!$response->isSucceeded()) {
            log_output(['error' => 'location-updated success message cannot send.'], __LINE__);
        }

        // --------------------
        // 成功メッセージ
        // --------------------

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('位置情報を設定しました。次に食べたい料理の名前を送信してください。');
        $response = $bot->replyMessage($replyToken, $textMessageBuilder);

        if (!$response->isSucceeded()) {
            log_output(['error' => 'location-update-success message cannot send.'], __LINE__);
        }
    }
}