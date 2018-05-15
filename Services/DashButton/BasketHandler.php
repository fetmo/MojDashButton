<?php

namespace mojDashButton\Services\DashButton;


use mojDashButton\Models\DashButton;

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
     * @return bool
     * @throws \Exception
     */
    public function addProductForButton(DashButton $button)
    {
        $insertArguments = [
            'button_id' => $button->getId(),
            'quantity' => $button->getQuantity(),
            'basket_date' => date("Y-m-d H:i:s"),
            'user_id' => $button->getUserId(),
            'ordernumber' => $button->getOrdernumber()
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
        $this->eventManager->notify('DashButton_AddToBasket_FilterArguments', $insertArguments);

        return $insertSuccess;
    }

    public function getProductsForUser($userID)
    {
        $selectBasket = 'SELECT * FROM moj_basket_details WHERE user_id = :userID';

        return $this->db->fetchAll(
            $selectBasket,
            ['userID' => $userID]
        );
    }

    public function createOrder($products, $userID)
    {
        return false;
    }

}