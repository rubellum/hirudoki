<?php

namespace Hirudoki\Subscriber;

use Hirudoki\LargeCategory;
use Hirudoki\SmallCategory;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class Text
{
    protected $bot;
    protected $event;

    public function __construct(LINEBot $bot, TextMessage $event)
    {
        $this->bot = $bot;
        $this->event = $event;
    }

    public function execute()
    {
        $bot = $this->bot;
        $event = $this->event;

        $userId = $event->getUserId();
        $replyToken = $event->getReplyToken();

        // ==============================
        // テキスト検索
        // ==============================

        $user_location = \ORM::for_table('user_location')->where('user_id', $userId)->order_by_desc('created_at')->find_array();
        $user_location = reset($user_location);

        // 位置情報が未登録
        if (!$user_location) {
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('まず最初に現在の「位置情報」を送信してください。位置情報は「＋」メニューから送信できます。');
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);

            if (!$response->isSucceeded()) {
                log_output(['error' => 'location not found message cannot send.'], __LINE__);
            }

            return;
        }

//        log_output(['user_location' => $user_location], __LINE__);

        $search_keyword = $event->getText();

//        log_output(['search_keyword' => $search_keyword], __LINE__);

        // --------------------
        // リッチメニュー検索
        // --------------------

        $p = new \Hirudoki\SearchParameter();
        $p->setLatitude($user_location['latitude']);
        $p->setLongitude($user_location['longitude']);
        $p->setRange('2');
        $p->setLunch('1');
        $p->setLimit(3);

        if ($l_result = LargeCategory::search($search_keyword)) {
            $p->setLargeCategoryCode($l_result['code']);
        } elseif ($s_result = SmallCategory::search($search_keyword)) {
            $p->setSmallCategoryCode($s_result['code']);
        } else {
            $p->setFreeword($search_keyword);
        }

        log_output(['p' => $p], __LINE__);

        $result = \Hirudoki\Client::getStoreList($p);

        log_output(['result' => $result], __LINE__);

        if (empty($result['items'])) {
            $textMessageBuilder = new LINEBot\MessageBuilder\TextMessageBuilder('店舗が見つかりませんでした。検索ワードを変えて、再度検索してみてください。');
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);

            if (!$response->isSucceeded()) {
                log_output(['error' => 'store-not-found message cannot send.'], __LINE__);
            }

            return;
        }

        $columns = [];
        foreach ($result['items'] as $store) {

            // カルーセルに付与するボタンを作る
            $action = new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("ぐるなびを見る", $store->url );

            // カルーセルのカラムを作成する
            $column    = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($store->name, $store->getPR(), $store->getImageUrl(), [$action]);
            $columns[] = $column;
        }

//        log_output(['columns' => $columns], __LINE__);

        // カラムの配列を組み合わせてカルーセルを作成する
        $carousel = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columns);

        // カルーセルを追加してメッセージを作る
        $carousel_message = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("「" . $search_keyword . "」検索結果", $carousel);

        $message = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
        $message->add($carousel_message);

        $res = $bot->replyMessage($replyToken, $message);

        log_output(['line:result' => $res], __LINE__);

        if (!$res->isSucceeded()) {
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('エラーが発生しました。再度、メッセージを送信してください。');
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);

            if (!$response->isSucceeded()) {
                log_output(['error' => 'line-failed-message cannot send.'], __LINE__);
            }
        }

        $user_event = \ORM::for_table('user_events')->create();
        $user_event->user_id = $userId;
        $user_event->type = 'search';
        $user_event->data = json_encode(['text' => $search_keyword], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $user_event->created_at = date('Y-m-d H:i:s');

        $result = $user_event->save();

        if (!$result) {
            log_output(['error' => "user({$userId}) cannot create location"], __LINE__);
        }
    }
}