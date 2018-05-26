<?php

namespace MojDashButton\Test\Integration\Services\Helper;

use MojDashButton\Models\DashButton;
use MojDashButton\Models\DashButtonProduct;
use Shopware\Components\DependencyInjection\Container;

trait ButtonCodeGenerator
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @return string
     */
    private function getButtonCode()
    {
        return 'PHPUNIT' . time() . rand(100, 20000);
    }

    /**
     * @param $buttons
     */
    private function removeButtons(array $buttons)
    {
        $em = $this->container->get('models');

        /** @var DashButton $button */
        foreach ($buttons as $button) {
            $em->remove($button);
            $em->flush($button);
        }
    }

    /**
     * @param string $buttoncode
     * @param array $products
     * @param int $productmode
     * @return DashButton
     */
    private function createButton(string $buttoncode, array $products = [], $productmode = DashButton::SINGLEPRODUCTMODE)
    {
        $em = $this->container->get('models');

        $button = new DashButton();
        $button->fromArray([
            'buttoncode' => $buttoncode,
            'userId' => 1,
            'productMode' => $productmode,
            'user'=> $em->getRepository(\Shopware\Models\Customer\Customer::class)->find(1)
        ]);

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