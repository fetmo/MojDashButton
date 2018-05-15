<?php


class Shopware_Controllers_Frontend_DashCenterBasket extends Shopware_Controllers_Frontend_Account
{

    public function overviewAction()
    {
        $basketHandler = $this->get('moj_dash_button.services.dash_button.basket_handler');

        $products = $basketHandler->getProductsForUser($this->getUserId());

        $this->View()->assign('products', $products);
    }


    private function getUserId()
    {
        $userData = $this->View()->getAssign('sUserData');

        return (int)$userData['additional']['user']['userID'];
    }

}