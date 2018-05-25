<?php

namespace MojDashButton\Test\Integration\Services\Helper;

use MojDashButton\Models\DashButton;
use MojDashButton\Models\DashButtonProduct;

trait ButtonCodeGenerator
{

    private function getButtonCode()
    {
        return 'PHPUNIT' . time() . rand(100, 20000);
    }

    private function removeButtons($buttons)
    {
        /** @var DashButton $button */
        foreach ($buttons as $button) {
            $this->db->delete('moj_basket_details', 'button_id = ' . $button->getId());
            $this->db->delete('moj_dash_button_product', 'button_id = ' . $button->getId());
            $this->db->delete('moj_dash_log', 'button_id = ' . $button->getId());
            $this->db->delete('moj_dash_button', 'id = ' . $button->getId());
        }
    }

    private function createButton($buttoncode, array $products = [], $productmode = \MojDashButton\Models\DashButton::SINGLEPRODUCTMODE)
    {
        $em = $this->container->get('models');

        /** @var DashButton $button */
        $button = $this->register->registerButton($buttoncode);

        $button->setUserId(1);
        $button->setProductMode($productmode);
        $button->setUser($em->getRepository(\Shopware\Models\Customer\Customer::class)->find(1));

        $em->persist($button);
        $em->flush($button);

        foreach ($products as $product) {
            $dashProduct = new DashButtonProduct();
            $dashProduct->setOrdernumber($product['ordernumber']);
            $dashProduct->setQuantity($product['quantity']);
            $dashProduct->setButton($button);

            $em->persist($dashProduct);
            $em->flush($dashProduct);
        }

        return $button;
    }
}