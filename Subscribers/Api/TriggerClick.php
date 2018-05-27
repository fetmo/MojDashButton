<?php

namespace MojDashButton\Subscribers\Api;

use Enlight\Event\SubscriberInterface;
use MojDashButton\Services\DashButton\BasketValidationService;
use MojDashButton\Services\DashButton\OrderHandler;

class TriggerClick implements SubscriberInterface
{

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @var BasketValidationService
     */
    private $basketValidation;

    public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db, OrderHandler $orderHandler,
                                BasketValidationService $basketValidation)
    {
        $this->db = $db;
        $this->orderHandler = $orderHandler;
        $this->basketValidation = $basketValidation;
    }

    public static function getSubscribedEvents()
    {
        return [
            'DashButton_AddToBasket_Finish' => 'createOrder'
        ];
    }

    public function createOrder(\Enlight_Event_EventArgs $args)
    {
        $basketEntry = $args->get('basket_entry');

        $userId = $basketEntry['user_id'];
        $basketPositionId = $basketEntry['basketId'];
        $dashProductId = $basketEntry['dashproduct_id'];

        $autoTrigger = $this->db->fetchOne(
            'SELECT moj_dash_button_directorder FROM s_user_attributes',
            ['user' => $userId]
        );

        if ((boolean)$autoTrigger) {
            $basketDetails = $this->basketValidation->validateSelectedProducts([$basketEntry]);
            $this->db->delete('moj_basket_details', 'id = ' . $basketPositionId);

            $basketPosition = [];
            foreach ($basketDetails as $basketDetail) {
                $basketPosition[] = [
                    'ordernumber' => $basketDetail['ordernumber'],
                    'quantity' => $basketDetail['purchase_quantity'],
                    'dashproductid' => $dashProductId
                ];
            }

            return $this->orderHandler->createOrder($basketPosition, $userId, true);
        }

        return false;
    }

}
