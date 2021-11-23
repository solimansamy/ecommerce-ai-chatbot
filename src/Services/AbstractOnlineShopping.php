<?php


namespace App\Services;


class AbstractOnlineShopping implements OnlineShoppingInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    protected function switchIntent($request, $intent)
    {
        $adapter = $this->container->get(ChannelRequestAdapter::class);
        switch ($intent) {
            case "Default Welcome Intent":
                return $adapter->welcomeIntent($request , $this->WelcomeIntent());
            case "Default Fallback Intent":
            case "empty":
                return $adapter->fallbackIntent($request , $this->FallbackIntent());
            case "cart.check":
                return $adapter->cartCheck($request , $this->CartCheckIntent());
            case "check_out":
                return $adapter->cartCheckout($request , $this->CheckOutIntent());
            case "delivery.options":
                return $adapter->deliveryOptions($request , $this->DeliveryOptionsIntent());
            case "gift_card":
                return $adapter->giftCard($request, $this->GiftCardIntent());
            case "item.add":
                return $adapter->addItem($request , $this->ItemAddIntent());
            case "item.remove":
                return $adapter->removeItem($request , $this->ItemRemoveIntent());
            case "item.swap":
                return $adapter->swapItem($request, $this->ItemSwapIntent());
            case "order.cancel":
                return $adapter->orderCancel($request, $this->OrderCancelIntent());
            case "order.change":
                return $adapter->orderChange($request, $this->OrderChangeIntent());
            case "order.status":
                return $adapter->orderStatus($request, $this->OrderStatusIntent());
            case "product.search":
                return $adapter->productSearch($request, $this->SearchProductIntent());
            case "refund.policy":
                return $adapter->refundPolicy($request, $this->RefundPolicyIntent());
            case "refund.request":
                return $adapter->refundRequest($request, $this->RefundRequestIntent());
            case "special_offers":
                return $adapter->specialOffer($request, $this->SpecialOffersIntent());
            case "order.history":
                return $adapter->orderHistory($request, $this->OrderHistoryIntent());
            case "order.receipt":
                return $adapter->orerReceipt($request, $this->OrderReceiptIntent());
            default:
                return $adapter->fallbackIntent($request, false);
        }
    }

}