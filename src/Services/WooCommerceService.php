<?php


namespace App\Services;

use App\Services\DialogFlow\Response;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;

class WooCommerceService
{
    const CONSUMER_KEY = 'ck_40b82550cc9b8049f85dba791cd26bb1ea3a2de1';

    const CONSUMER_SECRET = 'cs_78b89805fc0f6fbbd7026eb2b7221515c791f264';

    const BASE_URL = 'https://woocommerce.botme.com';

    /**
     * @param Response $response
     */
    public function getResult($response)
    {
        switch ($response->getIntent()) {
            case "product.search":
                return $this->productSearch($response->getParameters());

             default:
                return '';

//            case "Default Welcome Intent":
//                return $adapter->welcomeIntent($request , $this->WelcomeIntent());
//            case "Default Fallback Intent":
//            case "empty":
//                return $adapter->fallbackIntent($request , $this->FallbackIntent());
//            case "cart.check":
//                return $adapter->cartCheck($request , $this->CartCheckIntent());
//            case "check_out":
//                return $adapter->cartCheckout($request , $this->CheckOutIntent());
//            case "delivery.options":
//                return $adapter->deliveryOptions($request , $this->DeliveryOptionsIntent());
//            case "gift_card":
//                return $adapter->giftCard($request, $this->GiftCardIntent());
//            case "item.add":
//                return $adapter->addItem($request , $this->ItemAddIntent());
//            case "item.remove":
//                return $adapter->removeItem($request , $this->ItemRemoveIntent());
//            case "item.swap":
//                return $adapter->swapItem($request, $this->ItemSwapIntent());
//            case "order.cancel":
//                return $adapter->orderCancel($request, $this->OrderCancelIntent());
//            case "order.change":
//                return $adapter->orderChange($request, $this->OrderChangeIntent());
//            case "order.status":
//                return $adapter->orderStatus($request, $this->OrderStatusIntent());
//            case "product.search":
//                return $adapter->productSearch($request, $this->SearchProductIntent());
//            case "refund.policy":
//                return $adapter->refundPolicy($request, $this->RefundPolicyIntent());
//            case "refund.request":
//                return $adapter->refundRequest($request, $this->RefundRequestIntent());
//            case "special_offers":
//                return $adapter->specialOffer($request, $this->SpecialOffersIntent());
//            case "order.history":
//                return $adapter->orderHistory($request, $this->OrderHistoryIntent());
//            case "order.receipt":
//                return $adapter->orerReceipt($request, $this->OrderReceiptIntent());
//            default:
//                return $adapter->fallbackIntent($request, false);
        }
    }

    private function productSearch($params)
    {
        $search = $params['product'];
        $items = file_get_contents(
            sprintf("%s/wp-json/wc/v3/products?search=%s&consumer_key=%s&consumer_secret=%s", self::BASE_URL, $search, self::CONSUMER_KEY, self::CONSUMER_SECRET)
        );

        if(count($items) == 0) {
            return;
        }

        $elements = array(); $i = 0;
        while ($i < 5) {
            $item = $items[$i];
            $elements[] = Element::create($item['name'])
                ->subtitle('All about BotMan')
                ->image('http://botman.io/img/botman-body.png')
                ->addButton(ElementButton::create('visit')
                    ->url('http://botman.io')
                )
                ->addButton(ElementButton::create('tell me more')
                    ->payload('tellmemore')
                    ->type('postback')
                );
            $i++;
        }

        return GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements($elements);
    }
}
