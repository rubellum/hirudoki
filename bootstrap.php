<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

\ORM::configure('mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'));
\ORM::configure('username', getenv('MYSQL_USER'));
\ORM::configure('password', getenv('MYSQL_PASS'));

$settings = require __DIR__ . '/config/local.php';

$container = new \Slim\Container($settings);

$container['bot'] = $container->factory(function () {
    return new \LINE\LINEBot(new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN')), [
        'channelSecret' => getenv('CHANNEL_SECRET'),
    ]);
});

$app = new \Slim\App($container);

$container = $app->getContainer();

// -------------------------
// メーセージ受信＆検索実行
// -------------------------
$app->any('/callback/', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {

    /** @var \LINE\LINEBot $bot */
    $bot = $this->get('bot');

    $signature = $request->getHeader(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);
    $signature = is_array($signature) ? $signature[0] : $signature;

    log_output(['signature' => $signature], __LINE__);

    try {
        $events = $bot->parseEventRequest($request->getBody(), $signature);
    } catch (Exception $e) {
        log_output(['ERROR' => $e->getMessage()], __LINE__);
        die;
    }

    foreach ($events as $event) {
        log_output($event, __LINE__);

        $subscruber = \Hirudoki\Subscriber\SubscriberFactory::create($bot, $event);
        $subscruber->execute();
    }
});

// -------------------------
// 友達紹介ボタン
// -------------------------
$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $response->write('<h1 style="margin: 10px 0 -30px 0">昼ドキLINEBOT</h1><img width="200" src="./images/1.png"><br><a href="https://line.me/R/ti/p/%40isi4206g"><img height="36" border="0" alt="友だち追加" src="https://scdn.line-apps.com/n/line_add_friends/btn/ja.png"></a><br><br>Powered by <a href="http://www.gnavi.co.jp/">ぐるなび</a> - ぐるなびAPIを使用しています。');
});

return $app;
