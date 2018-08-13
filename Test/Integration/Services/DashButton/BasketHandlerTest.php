<?php

use \MojDashButton\Models\DashButton;
use \MojDashButton\Services\DashButton as DBServices;

class BasketHandlerTest extends PHPUnit_Framework_TestCase
{

    use \MojDashButton\Test\Integration\Services\Helper\ButtonCodeGenerator;

    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Shopware()->Container();

        $this->db = $this->container->get('db');
    }

    public function testAddProductToDashBasket()
    {
        $products = [
            ['ordernumber' => 'SW10118.11', 'quantity' => 10]
        ];

        $button = $this->createButton($this->getButtonCode(), $products);
        $button->setUserId(-100);

        $this->assertTrue($this->getBasketHandler()->addProductForButton($button, $button->getProducts()[0]));

        return $button;
    }

    /**
     * @depends testAddProductToDashBasket
     *
     * @param DashButton  $button
     * @return DashButton
     */
    public function testCollectProductsForButton(DashButton $button)
    {
        $products = $this->getBasketHandler()->getProductsForButton($button);

        $this->assertCount(1, $products);

        return $button;
    }

    /**
     * @depends testCollectProductsForButton
     *
     * @param $button
     */
    public function testCollectProductsForUser(DashButton $button)
    {
        $products = [
            ['ordernumber' => 'SW10118.10', 'quantity' => 10],
            ['ordernumber' => 'SW10119', 'quantity' => 5],
        ];

        $button2 = $this->createButton($this->getButtonCode(), $products, DashButton::MULTIPRODUCTMODE);
        $button2->setUserId(-100);

        $basketHandler = $this->getBasketHandler();

        $this->assertTrue($basketHandler->addProductForButton($button2, $button2->getProducts()[0]));
        $this->assertTrue($basketHandler->addProductForButton($button2, $button2->getProducts()[1]));

        $this->assertTrue($basketHandler->addProductForButton($button, $button->getProducts()[0]));

        $products = $basketHandler->getProductsForUser($button2->getUserId());
        $ordernumbers = array_column($products, 'ordernumber');

        $this->assertCount(4, $products);
        $this->assertContains('SW10118.10', $ordernumbers);
        $this->assertContains('SW10119', $ordernumbers);
        $this->assertContains('SW10118.11', $ordernumbers);

        $this->removeButtons([$button, $button2]);
    }

    private function getBasketHandler()
    {
        return new DBServices\BasketHandler($this->db, $this->container->get('events'));
    }

}