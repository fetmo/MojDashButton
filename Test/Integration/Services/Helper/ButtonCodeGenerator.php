<?php

namespace MojDashButton\Test\Integration\Services\Helper;

use MojDashButton\Models\DashButton;
use MojDashButton\Models\DashButtonProduct;
use MojDashButton\Models\DashButtonRule;
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
            'userId' => $this->getUserId(),
            'productMode' => $productmode,
            'user'=> $em->getRepository(\Shopware\Models\Customer\Customer::class)->find($this->getUserId())
        ]);

        $em->persist($button);
        $em->flush($button);

        foreach ($products as $product) {
            $rules = $product['rules'];
            unset($product['rules']);

            $dashProduct = new DashButtonProduct();
            $dashProduct->fromArray(array_merge($product, [
                'button' => $button
            ]));

            $em->persist($dashProduct);
            $em->flush($dashProduct);

            foreach ($rules as $rule) {
                $dashRule = new DashButtonRule();
                $dashRule->fromArray(array_merge($rule, [
                    'product' => $dashProduct
                ]));

                $em->persist($dashRule);
                $em->flush($dashRule);
            }
        }

        return $button;
    }

    private function getUserId()
    {
        return 1;
    }
}