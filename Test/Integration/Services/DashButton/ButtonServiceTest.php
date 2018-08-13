<?php

class ButtonServiceTest extends PHPUnit_Framework_TestCase
{

    use \MojDashButton\Test\Integration\Services\Helper\UserHelper;

    /**
     * @var Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * @var array
     */
    private $buttons;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->container = Shopware()->Container();

        $this->db = $this->container->get('db');
    }

    protected function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub

        if (isset($this->buttons)) {
            $this->removeButtons($this->buttons);
        }
    }

    public function testGetProductForButton()
    {
        $products = [
            ['ordernumber' => 'SW10118.10', 'quantity' => 10]
        ];

        $this->unableAutoOrder();
        $button = $this->createButton($this->getButtonCode(), $products);

        $token = $this->getAuthService()->generateToken($button->getButtonCode());

        $productData = $this->getButtonService()->getProduct($token);

        $this->assertNotEmpty($productData['id']);
        $this->assertNotEmpty($productData['title']);
        $this->assertNotEmpty($productData['price']);
        $this->assertNotEmpty($productData['quantity']);
        $this->assertNotEmpty($productData['identifier']);

        return [$token, $button];

    }

    /**
     * @depends testGetProductForButton
     * @param $data
     * @return \MojDashButton\Models\DashButton
     */
    public function testTriggerClickForButton($data)
    {
        $token = $data[0];
        $button = $data[1];

        $this->assertTrue($this->getButtonService()->triggerClick($token));

        return $button;
    }

    /**
     * @depends testTriggerClickForButton
     * @param $firstButton
     * @return array
     */
    public function testGetProductForMultiProductButton($firstButton)
    {
        $products = [
            ['ordernumber' => 'SW10118.10', 'quantity' => 10],
            ['ordernumber' => 'SW10119', 'quantity' => 5],
        ];

        $button = $this->createButton($this->getButtonCode(), $products, \MojDashButton\Models\DashButton::MULTIPRODUCTMODE);

        $token = $this->getAuthService()->generateToken($button->getButtonCode());

        $productData = $this->getButtonService()->getProduct($token);
        $identifier = '';

        $this->assertCount(2, $productData);

        foreach ($productData as $key => $product) {
            $origProduct = $products[$key];

            $this->assertNotEmpty($product['id']);
            $this->assertNotEmpty($product['title']);
            $this->assertNotEmpty($product['price']);
            $this->assertNotEmpty($product['quantity']);
            $this->assertEquals($product['quantity'], $origProduct['quantity']);
            $this->assertNotEmpty($product['identifier']);

            $identifier = $product['identifier'];
        }

        return [
            [$firstButton, $button],
            $token,
            $identifier
        ];
    }

    /**
     * @depends testGetProductForMultiProductButton
     * @param $data
     * @return array
     */
    public function testTriggerClickForMultiProductButton($data)
    {
        list($buttons, $token, $identifier) = $data;

        $this->assertTrue($this->getButtonService()->triggerClick($token, $identifier));

        return $buttons;
    }

    /**
     * @depends testTriggerClickForMultiProductButton
     * @param array $oldButtons
     */
    public function testGetNoProductForUnconfiguredButton($oldButtons)
    {
        $this->expectException(\Exception::class);

        $button = $this->createButton($this->getButtonCode());
        $this->buttons = $oldButtons;
        $this->buttons[] = $button;

        $this->revertAutoOrder();

        $token = $this->getAuthService()->generateToken($button->getButtonCode());

        $this->getButtonService()->getProduct($token);
    }

    /**
     * @depends testGetNoProductForUnconfiguredButton
     */
    public function testTriggerClickNoProductForUnconfiguredButton()
    {
        $this->expectException(\Exception::class);

        $button = $this->createButton($this->getButtonCode());
        $this->buttons = [$button];

        $token = $this->getAuthService()->generateToken($button->getButtonCode());

        $this->getButtonService()->triggerClick($token);
    }


    /**
     * @return mixed|\MojDashButton\Services\DashButton\ButtonService
     */
    private function getButtonService()
    {
        $buttonservice = $this->container->get('moj_dash_button.services.dash_button.button_service');
        return $buttonservice;
    }

    /**
     * @return mixed|\MojDashButton\Services\Api\AuthenticationService
     */
    private function getAuthService()
    {
        $authService = $this->container->get('moj_dash_button.services.api.authentication_service');
        return $authService;
    }
}