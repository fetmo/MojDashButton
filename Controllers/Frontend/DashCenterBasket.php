<?php


class Shopware_Controllers_Frontend_DashCenterBasket extends Shopware_Controllers_Frontend_Account
{

    public function overviewAction()
    {
        $basketHandler = $this->get('moj_dash_button.services.dash_button.basket_handler');

        $products = $basketHandler->getProductsForUser($this->getUserId());

        $this->View()->assign('products', $products);
    }


    public function confirmAction()
    {
        $products = $this->Request()->getPost('products', []);

        $selected = array_filter($products, function ($el) {
            return isset($el['checked']);
        });

        $positionIds = array_column($selected, 'id');

        $this->get('db')->delete(
            'moj_basket_details',
            'id IN (' . substr(json_encode($positionIds), 1, -1) . ')'
        );

        $basket = $this->get('moj_dash_button.services.dash_button.basket_validation_service')->validateSelectedProducts($selected);

        $validation = count(array_filter(array_column($basket, 'validation'))) === 0;

        $this->View()->assign([
            'basket' => $basket,
            'basketValid' => $validation
        ]);
    }

    public function finishOrderAction()
    {
        $products = $this->Request()->get('products');

        $orderhandler = $this->get('moj_dash_button.services.dash_button.order_handler');
        $orderhandler->createOrder($products, $this->getUserId());

        $this->redirect([
            'controller' => 'Account',
            'action' => 'orders'
        ]);
    }

    private function getUserId()
    {
        $userData = $this->View()->getAssign('sUserData');

        return (int)$userData['additional']['user']['userID'];
    }

}