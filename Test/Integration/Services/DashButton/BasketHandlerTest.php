<?php

class BasketHandlerTest extends PHPUnit_Framework_TestCase
{

    use \mojDashButton\Test\Integration\Services\Helper\ButtonCodeGenerator;

    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \mojDashButton\Services\DashButton\DbRegisterService
     */
    private $register;

    /**
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->container = Shopware()->Container();

        $this->db = $this->container->get('db');
        $this->register = $this->container->get('moj_dash_button.services.dash_button.db_register_service');
    }

    public function testAddProductToDashBasket()
    {
        $button = $this->register->registerButton($this->getButtonCode());

        $button->setOrdernumber('SWAG-2000');
        $button->setUserId(-100);

        $this->assertTrue($this->getBasketHandler()->addProductForButton($button));

        return $button;
    }

    /**
     * @depends testAddProductToDashBasket
     *
     * @param $button
     */
    public function testCollectProductsForButton($button)
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
    public function testCollectProductsForUser($button)
    {
        $button2 = $this->register->registerButton($this->getButtonCode());

        $button2->setOrdernumber('SWAG-2002');
        $button2->setUserId(-100);

        $basketHandler = $this->getBasketHandler();

        $this->assertTrue($basketHandler->addProductForButton($button2));

        $this->assertTrue($basketHandler->addProductForButton($button));

        $products = $basketHandler->getProductsForUser($button2->getUserId());
        $ordernumbers = array_column($products, 'ordernumber');

        $this->assertCount(3, $products);
        $this->assertContains('SWAG-2002', $ordernumbers);
        $this->assertContains('SWAG-2000', $ordernumbers);

        $this->removeButtons([$button, $button2]);
    }

    private function getBasketHandler()
    {
        return new \mojDashButton\Services\DashButton\BasketHandler($this->db, $this->container->get('events'));
    }

    private function removeButtons($buttons)
    {
        foreach ($buttons as $button) {
            $this->db->delete('moj_basket_details', 'button_id = ' . $button->getId());
            $this->db->delete('moj_dash_log', 'button_id = ' . $button->getId());
            $this->db->delete('moj_dash_button', 'id = ' . $button->getId());
        }
    }

}