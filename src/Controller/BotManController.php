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


class BotManController extends Controller
{
    const config = [
            'facebook' => [
                'token' => 'EAAGWjfdH1esBAHDLeCCKep3m3lNDdgZApyiOQv3T0kUZBl5FKMpszKSyGwaXekMRl4nMG9UZAqsIZB3eKjvPZC2CjZARAEZATBjLVpAHcX0LUCb2qRyUZB76NLYBuTn4K7a5h4XhNkLIo567s57Sz3Debh2aacdaLALbZAMTeLh0X7TAzHy3x4VqqS2C2O6kl3IgZD',
                'app_secret' => '177e060025caac02951004d19dab37d1',
                'verification'=>'YESCOMIN',
            ]
        ];

    public function botmanWebhook(Request $request)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
        $botman = BotManFactory::create($this::config);
//        $middleware = new DialogFlowReceivedMiddleware();

//        dump($request->query->get('hub_challenge'));
//        $botman->listen();
//        echo $request->query->get('hub_challenge');
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });
        $botman->hears('[a-zA-Z]+', function (BotMan $bot) {
            $middleware = $this->get(DialogFlowReceivedMiddleware::class);
            $bot->middleware->received($middleware);
        });

        $botman->listen();

        return new Response();
    }

    public function testDialogFlow(Request $request)
    {
        $middleware = $this->get(DialogFlowReceivedMiddleware::class);
        $text = $request->query->get('text');
        $middleware->testDialogFlow($text);
        return new Response();
    }
}