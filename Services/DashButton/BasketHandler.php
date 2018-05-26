<?php

namespace MojDashButton\Services\DashButton;


use MojDashButton\Models\DashButton;
use MojDashButton\Models\DashButtonProduct;

class BasketHandler
{

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \Enlight_Event_EventManager
     */
    private $eventManager;

    /**
     * BasketHandler constructor.
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     * @param \Enlight_Event_EventManager $eventManager
     */
    public function __construct(\Enlight_Components_Db_Adapter_Pdo_Mysql $db, \Enlight_Event_EventManager $eventManager)
    {
        $this->db = $db;
        $this->eventManager = $eventManager;
    }

    /**
     * @param DashButton $button
     * @param DashButtonProduct $buttonProduct
     * @return bool
     * @throws \Exception
     */
    public function addProductForButton(DashButton $button, DashButtonProduct $buttonProduct)
    {
        $insertArguments = [
            'button_id' => $button->getId(),
            'quantity' => $buttonProduct->getQuantity(),
            'basket_date' => date("Y-m-d H:i:s"),
            'user_id' => $button->getUserId(),
            'ordernumber' => $buttonProduct->getOrdernumber(),
            'dashproduct_id' => $buttonProduct->getId()
        ];

        $insertArguments = $this->eventManager->filter('DashButton_AddToBasket_FilterArguments', $insertArguments, [
            'button' => $button
        ]);

        $insertSuccess = (bool)$this->db->insert(
            'moj_basket_details',
            $insertArguments
        );

        if (false === $insertSuccess) {
            throw new \Exception('Add to Dash-Basket failed');
        }

        $insertArguments['basketId'] = $this->db->lastInsertId('moj_basket_details');
        $this->eventManager->notify('DashButton_AddToBasket_Finish', $insertArguments);

        return $insertSuccess;
    }

    public function getProductsForUser($userID)
    {
        $selectBasket =
            'SELECT button.button_code, basket.*
              FROM moj_basket_details basket
               INNER JOIN moj_dash_button button ON button.id = basket.button_id
               WHERE basket.user_id = :userID';

        return $this->db->fetchAll(
            $selectBasket,
            ['userID' => $userID]
        );
    }

    public function getProductsForButton(DashButton $button)
    {
        $selectBasket =
            'SELECT button.button_code, basket.*
              FROM moj_basket_details basket
               INNER JOIN moj_dash_button button ON button.id = basket.button_id
               WHERE basket.button_id = :buttonid';

        return $this->db->fetchAll(
            $selectBasket,
            ['buttonid' => $button->getId()]
        );
    }

    public function createOrder($products, $userID)
    {
        return false;
    }

}