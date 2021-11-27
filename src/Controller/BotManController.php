<?php


namespace App\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DialogFlowReceivedMiddleware;
use Psr\Log\LoggerInterface;
use function ProxyManagerTestAsset\selfAndBoolType;
use App\Services\DialogFlow\DialogFlow;
use BotMan\BotMan\Messages\Attachments\Audio;


class BotManController extends Controller
{
    const VERIFY_TOKEN = 'riseup';

    const config = [
        'facebook' => [
            'token' => 'EAADGZAvOUQeEBAPxKTVNdeq69tBfifNI5ENqfh69gGs0IJtfjsV0bmhkDINSfRjo2dxN5uKV0dQZCZBrLlBye9JZC0iJXvARBWlAkhEMM1smbxIPlpbmZCip5rmrtZC5XB286wzbqRCsIbgMF9YltZCRGkpdXKhwUKUyCKTfuQdZAKKwevbk3x2G',
            'app_secret' => 'a538436124b1d66710f7f14f2d2f2b73',
            'verification' => self::VERIFY_TOKEN
        ]
    ];

    public function messengerWebHook(Request $request, LoggerInterface $logger)
    {
        // Edit Callback URL
        if($request->isMethod('GET')) {
            $logger->debug('Request Logging: ' . $request);
            return $this->verifyMessengerWebHook($request);
        }

        // Messenger is talking
        if($request->isMethod('POST')) {
            if($this->shouldTheRequestBeBlocked($request)) {
                return new Response();
            }
            $logger->debug('Request Logging: ' . $request->getContent());
            return $this->listenToMessenger($request);
        }
    }

    private function shouldTheRequestBeBlocked(Request $request)
    {
        $json = json_decode($request->getContent());
        $timeStamp = $json->entry[0]->messaging[0]->timestamp;

        $TEN_MINUTES = 10 * 60 * 1000;
        $tenAgo = $timeStamp - $TEN_MINUTES;
        if ($timeStamp < $tenAgo) {
            return true;
        }

        return false;
    }

    private function verifyMessengerWebHook(Request $request)
    {
        if($request->query->get('hub_mode') == 'subscribe' && $request->query->get('hub_verify_token') == self::VERIFY_TOKEN) {
            return new Response($request->query->get('hub_challenge'), 200);
        }

        return new Response('Not Found', 404);
    }

    private function listenToMessenger(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        $botman = BotManFactory::create($this::config);
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        $dialogFlow = DialogFlow::create('en');
        $botman->middleware->received($dialogFlow);

        // Hearing Text
        $botman->hears('(.*)', function ($bot) {
            $extras = $bot->getMessage()->getExtras();
            $bot->reply($extras['wooCommerce']);
        })->middleware($dialogFlow);


        // Hearing Audio
//        $botman->receivesAudio(function($bot, $audios) {
//            foreach ($audios as $audio) {
//                $url = $audio->getUrl(); // The direct url
//                $payload = $audio->getPayload(); // The original payload
//                $bot->reply('HHHHHHHHHHHHHHH');
//            }
//        });

//        $botman->hears('[A-Za-z,;\'!"\\s.]+', function (BotMan $bot) {
//
//        });
//
//        $botman->hears('call me {name}', function ($bot, $name) {
//            $bot->reply('Your name is: '.$name);
//        });
//
        $botman->listen();
//
        return new Response();
    }

    public function testDialogFlow(Request $request)
    {
        $middleware = $this->get(DialogFlowReceivedMiddleware::class);
        $text = $request->query->get('text');
        $middleware->testDialogFlow($text);
    }
}
