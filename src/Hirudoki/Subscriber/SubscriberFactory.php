<?php

namespace Hirudoki\Subscriber;

use LINE\LINEBot;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

/**
 * Class SubscriberFactory
 * @package Hirudoki\Subscriber
 */
class SubscriberFactory
{
    /**
     * @param LINEBot $bot
     * @param BaseEvent $event
     * @return Location|Text|Unknown
     */
    public static function create(LINEBot $bot, $event)
    {
        if ($event instanceof LocationMessage) {
            return new Location($bot, $event);
        }

        if ($event instanceof TextMessage) {
            return new Text($bot, $event);
        }

        return new Unknown($bot, $event);
    }
}