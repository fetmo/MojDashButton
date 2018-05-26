<?php

use MojDashButton\Services\DashButton\DbCollector;

class DbCollectorTest extends PHPUnit_Framework_TestCase
{

    use \MojDashButton\Test\Integration\Services\Helper\ButtonCodeGenerator;

    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $em;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Shopware()->Container();
        $this->em = $this->container->get('models');
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

        $dashButton->setUserId(-100);
        $this->em->persist($dashButton);
        $this->em->flush($dashButton);

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

        $this->removeButtons([$button]);
    }

    private function getDbCollector()
    {
        return new DbCollector($this->em);
    }

}
