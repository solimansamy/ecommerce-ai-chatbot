<?php


namespace App\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class botmanController
{
    const config = [
            'facebook' => [
                'token' => 'yEAAGWjfdH1esBAL1O5FTy4rmpYH2zZAV2ff1abgXyCZAKRlAzxJlsFQPSsQZB0ChW9QZBwGTocLwxb2ZA1ig8Ghv2RZCMAGDjglZAkXJ21G8PS9lF4i6lKDUU14BiWvG7MrsEB4HttkIZBVEwy54hAEJCZA3xJxvZCeR7DVUC03MBrlcg0mmzTMbwuSZART5xWPyoGEZD',
                'app_secret' => '177e060025caac02951004d19dab37d1',
                'verification'=>'YESCOMIN',
            ]
        ];

    public function botmanWebhook(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        $botman = BotManFactory::create($this::config);
//        dump($request->query->get('hub_challenge'));
//        $botman->listen();
        echo $request->query->get('hub_challenge');
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });
//        $botman->listen();

        return new Response();
    }
}