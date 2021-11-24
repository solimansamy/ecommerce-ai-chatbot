<?php


namespace App\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Tgallice\Wit\Client;
use Tgallice\Wit\MessageApi;
use Tgallice\Wit\Model\Context;
use Tgallice\Wit\SpeechApi;

class witAIAgent
{
    protected $appToken ;
    protected $messageApi ;
    protected $clientApi ;
    protected $sessionId;
    protected $context ;
    protected $entities;
    protected $witResponse ;
    /**
     * @description
     * The maximum number of n-best trait entities you want to get back.
     * The default is 1, and the maximum is 8
     */
    private const n = 1 ;

    public function setupConfiguration($businessPurb)
    {
        $this->appToken = getenv('WIT_TOKEN_'.$businessPurb);
        $this->sessionId = uniqid();
        $this->context = new Context(['reference_time'=>'','timezone'=>'','locale'=>'ar_EG','coords'=>'']);
        return $this;
    }

    public function openSession($sessionId=false)
    {
        if($sessionId)
            $this->sessionId  = $sessionId ;
        $this->sessionId = $sessionId ;
        $this->clientApi = new Client($this->appToken);
        return $this ;
    }

    public function closeSession()
    {
        $this->sessionId = null ;
    }

    protected function detectIntent($input, $inputType, $languageCode='en-US')
    {
        if ($inputType == 'audio') {
            return $this->detectAudioIntent($input);
        }
        else {
            return $this->detectIntentText($input);
        }

    }

    protected function detectIntentText($text)
    {
        $messageApi = new MessageApi($this->clientApi);
        return $messageApi->extractMeaning($text);
    }

//    protected function detectAudioIntent($audioFilePath )
//    {
//        $wavFile = $this->container->get(AudioMessageAdapter::class)->convert($audioFilePath, 'wav');
//        $speechApi = new SpeechApi($this->clientApi);
//        return $speechApi->extractMeaning($wavFile);
//    }

    protected function handleResponse($response ,EntityModel &$entities)
    {
        //TODO : CORNER CASES NEED TO BE HANDLED
        foreach ($response as $key => $value )
        {
            $entities->setIntentName($key);
            $entities->setConfidence($value['confidence']);
            $entities->setValue($value['value']);
            if (isset($value[0]['entities']))
            {
                $entity = new EntityModel() ;
                $entities->setSubEntity($entity);
                $this->handleResponse($value[0]['entities'] , $entity);
            }
        }
        return $entities ;
    }

    public function WelcomeIntent()
    {
        $content = $this->detectedContent();
        $content->setFulfilment('ازيك! قولى اقدر اساعدك ازاى ؟ ');
        return $content ;
    }
    public function FallbackIntent()
    {
        $content = new EntityModel();
        $content->setFulfilment('مش فاهمك');
        return $content ;
    }

    protected function detectedContent()
    {
        $content = new EntityModel();
        $content->setQueryText($this->witResponse['_text']);
        $content->setIntentName($this->witResponse['entities']['intent'][0]['value']);
        $content->setConfidence($this->witResponse['entities']['intent'][0]['confidence']);
        return $content ;
    }
}