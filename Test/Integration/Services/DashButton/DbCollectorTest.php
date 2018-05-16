<?php

use mojDashButton\Services\DashButton\DbCollector;

class DbCollectorTest extends PHPUnit_Framework_TestCase
{

    use \mojDashButton\Test\Integration\Services\Helper\ButtonCodeGenerator;

    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $em;

    protected function setUp()
    {
        parent::setUp();

        $this->em = Shopware()->Models();
    }

    public function testButtonNotFoundForButtonCode()
    {
        $this->expectException(\Exception::class);

        $this->getDbCollector()->collectButton('ASDF' . time());
    }

    public function testButtonFoundForButtonCode()
    {
        $buttonCode = $this->getButtonCode();
        $dashButton = $this->createButton($buttonCode);

        $this->assertEquals($dashButton, $this->getDbCollector()->collectButton($buttonCode));

        return $dashButton;
    }

    /**
     * @depends testButtonFoundForButtonCode
     * @param $button
     */
    public function testButtonForUserFound($button)
    {
        $this->assertContains($button, $this->getDbCollector()->collectButtonForUser(-100));

        $this->deleteButton($button);
    }

    private function createButton($buttonCode)
    {
        $dashButton = new \mojDashButton\Models\DashButton();
        $dashButton->setButtonCode($buttonCode);
        $dashButton->setQuantity(1);
        $dashButton->setOrdernumber('SWAG-UNIT');
        $dashButton->setUserId(-100);

        $this->em->persist($dashButton);
        $this->em->flush($dashButton);

        return $dashButton;

    }

    private function deleteButton($button)
    {
        $this->em->remove($button);
        $this->em->flush($button);
    }

    private function getDbCollector()
    {
        return new DbCollector($this->em);
    }

}
