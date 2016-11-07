<?php

namespace Hirudoki\Subscriber;

use LINE\LINEBot;

/**
 * Class Unknown
 * @package Hirudoki\Subscriber
 */
class Unknown
{
    /**
     * @var LINEBot
     */
    protected $bot;


    /**
     * @var LINEBot\Event\BaseEvent
     */
    protected $event;

    /**
     * Unknown constructor.
     * @param LINEBot $bot
     * @param LINEBot\Event\BaseEvent $event
     */
    public function __construct(LINEBot $bot, LINEBot\Event\BaseEvent $event)
    {
        $this->bot = $bot;
        $this->event = $event;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        $bot = $this->bot;
        $event = $this->event;

        $userId = $event->getUserId();
        $replyToken = $event->getReplyToken();

        // --------------------
        // 不明なメッセージ
        // --------------------

        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('まず最初に現在の「位置情報」を送信してください。位置情報は「＋」メニューから送信できます。');
        $response = $bot->replyMessage($replyToken, $textMessageBuilder);

        if (!$response->isSucceeded()) {
            log_output(['error' => 'message-type-does-not-found message cannot send.'], __LINE__);
        }

        return true;
    }
}
