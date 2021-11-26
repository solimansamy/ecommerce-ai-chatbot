<?php

namespace App\Services;

use Google\Cloud\Dialogflow\V2\AudioEncoding;
use Google\Cloud\Dialogflow\V2\InputAudioConfig;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryResult;

class dialogFlowAgent
{
    /** @var SessionsClient */
    private $sessionsClient;

    /** @var string */
    private $sessionName ;

    /** @var string */
    private $projectId ;
    
    /** @var string */
    private $credentialPath;

    /** @var string */
    protected $sessionId ;

    /** @var string */
    protected $language;

    /** @var QueryResult */
    protected $dialogFlowResponse;

    public function setupConfiguration($businessPurb)
    {
        $this->sessionId      = uniqid() ;
        $this->projectId      = getenv('DIALOGFLOW_ID');
//        $this->credentialPath = getenv('DIALOGFLOW_PATH_'.$businessPurb);
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId ;
    }

    /**
     * @param bool $sessionId
     * @return $this
     * @throws \Google\ApiCore\ValidationException
     */
    public function openSession($sessionId = false)
    {
        if($sessionId) {
            $this->sessionId  = $sessionId;
        }
        $credential = array('credentials' => '/home/soliman/Projects/Riseup/woocommerce-chatbot-v9sf-5ffd1ecea7c5.json');
        $this->sessionsClient = new SessionsClient($credential);
        $this->sessionName = $this->sessionsClient->sessionName('woocommerce-chatbot-v9sf', uniqid());
        return $this ;
    }

    public function closeSession()
    {
        $this->sessionsClient->close();
    }

    /**
     * @param $input
     * @param $inputType
     * @param string $languageCode
     * @return QueryResult
     * @throws \Google\ApiCore\ApiException
     */
    public function detectIntent($input, $inputType, $languageCode='en-US')
    {
        if ($inputType == 'audio') {
            return $this->detectAudioIntent($input, $languageCode);
        }
        else {
            return $this->detectIntentText($input, $languageCode);
        }
    }

    /**
     * @param $text
     * @param $languageCode
     * @return QueryResult
     * @throws \Google\ApiCore\ApiException
     */
    protected function detectIntentText($text, $languageCode)
    {
        $textInput = new TextInput();
        $textInput->setText($text);
        $textInput->setLanguageCode($languageCode);
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        $response = $this->sessionsClient->detectIntent($this->sessionName, $queryInput);
        dump($response->getQueryResult());exit;
        return  $response->getQueryResult();
    }

    /**
     * @param $audioFilePath
     * @param $languageCode
     * @return QueryResult
     * @throws \Google\ApiCore\ApiException
     */
    protected function detectAudioIntent($audioFilePath, $languageCode)
    {
        $flacFile = $this->container->get(AudioMessageAdapter::class)->convert($audioFilePath, 'flac');
        $audioConfig = new InputAudioConfig();
        $audioConfig->setAudioEncoding(AudioEncoding::AUDIO_ENCODING_FLAC);
        $audioConfig->setLanguageCode($languageCode);
        $audioConfig->setSampleRateHertz(44100);
        $queryInput = new QueryInput();
        $queryInput->setAudioConfig($audioConfig);
        $response = $this->sessionsClient->detectIntent($this->sessionName, $queryInput, ['inputAudio' => $flacFile]);
        return  $response->getQueryResult();
    }

    public function WelcomeIntent()
    {
        $content = new EntityModel();
        $content->setQueryText($this->dialogFlowResponse->getQueryText());
        $content->setIntentName($this->dialogFlowResponse->getIntent()->getDisplayName());
        $content->setConfidence($this->dialogFlowResponse->getIntentDetectionConfidence());
        $content->setFulfilment($this->dialogFlowResponse->getFulfillmentText());
        return $content ;
    }

    public function FallbackIntent()
    {
        $content = new EntityModel();
        $content->setQueryText($this->dialogFlowResponse->getQueryText());
        $content->setIntentName($this->dialogFlowResponse->getIntent()->getDisplayName());
        $content->setFulfilment($this->dialogFlowResponse->getFulfillmentText());
        return $content ;
    }

    protected function ExtractParams()
    {
        $params = array();
        $paramsObject = $this->dialogFlowResponse->getParameters()->getFields();
        foreach ($paramsObject as $key => $value) {
            if($paramsObject[$key]->getKind()=='string_value')
                $params[$key] =  $paramsObject[$key]->getStringValue();
            //in some cases it's a struct value like "location"
        }
        return $params ;
    }

    protected function detectedContent()
    {
        $content = new EntityModel();
        $content->setQueryText($this->dialogFlowResponse->getQueryText());
        $content->setIntentName($this->dialogFlowResponse->getIntent()->getDisplayName());
        $content->setConfidence($this->dialogFlowResponse->getIntentDetectionConfidence());
        $content->setFulfilment($this->dialogFlowResponse->getFulfillmentText());
        return $content ;
    }
}
