<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use APP\Services\dialogFlowReceivedMiddleware;
use APP\Services\witAIReceivedMiddleware;

require dirname(__DIR__) . '/config/bootstrap.php';

     $config = [
        'facebook' => [
            'token' => 'EAAGWjfdH1esBAJ1Ath5RJrbLzZBLRyzE7VNyUhltaGsbttD3mLyCT2gq5zho3LepuH996p4ZChdqSKOLCZAR0B4aSj8kyODZCdhPqgT4qnGNtifK4mBtT3ZC8rc1dBF6cWaZB28tSvTZAxqajlpTCM4hPw9iG3Exi0TLoE0cnR5pChTkDkdlLZClMqZC7BseC5sIZD',
            'app_secret' => '177e060025caac02951004d19dab37d1',
            'verification'=>'YESCOMIN',
        ]
    ];
     //another// EAAGWjfdH1esBAJKtVLZAZAmkbtql9wp5pB7rFLYASzD8N24wCZAKzhkTc4k4rpzZCm11jU6wZAkQqQYjErJ6Ffg7HFQ1WctwdDOiRHLFZCNZAEw3c6ZAEOXTBytDkEW9YIEvfA2iIZAwZBLRb27JNT8fOdSFzw6MilJZAxEhS8lHGCtUwJCFif7vTUxtJJUIu5E91gZD
//    EAAGWjfdH1esBAJ1Ath5RJrbLzZBLRyzE7VNyUhltaGsbttD3mLyCT2gq5zho3LepuH996p4ZChdqSKOLCZAR0B4aSj8kyODZCdhPqgT4qnGNtifK4mBtT3ZC8rc1dBF6cWaZB28tSvTZAxqajlpTCM4hPw9iG3Exi0TLoE0cnR5pChTkDkdlLZClMqZC7BseC5sIZD

    DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

// Create an instance
    $botman = BotManFactory::create($config);

    // Give the bot something to listen for.
    $botman->hears('hello', function (BotMan $bot) {
        $bot->reply('Hello yourself.');
    });

    // Start listening

    $botman->hears('[a-zA-Z]+', function (BotMan $bot) {
        $middleware = new dialogFlowReceivedMiddleware();
        $bot->middleware->received($middleware);

    });

//    $middleware = new dialogFlowReceivedMiddleware();
    $middleware = new witAIReceivedMiddleware();
    $botman->middleware->received($middleware);

    $botman->listen();

