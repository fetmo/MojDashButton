<?php

class LoggerTest extends \PHPUnit_Framework_TestCase
{

    use \MojDashButton\Test\Integration\Services\Helper\ButtonCodeGenerator;

    /**
     * @var string
     */
    private $type = 'phpunit';

    /**
     * @var string
     */
    private $whereType;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Shopware()->Container();
        $this->db = $this->container->get('db');
        $this->whereType = 'type = "' . $this->type . '"';
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->db->delete('moj_dash_log', $this->whereType);
    }

    public function testLog()
    {
        $this->whereType = 'type = "' . $this->type . '"';

        $logger = new \MojDashButton\Services\Core\Logger($this->db);

        $logger->log($this->type, null, 'First Log');

        $logs = $this->db->fetchAll('SELECT * FROM moj_dash_log WHERE ' . $this->whereType);
        $this->assertCount(1, $logs);

        $logger->log($this->type, null, 'Another Log');
        $logger->log($this->type, new \MojDashButton\Models\DashButton(), 'Another Log');
        $logger->log($this->type, null, 'Another Log');
        $logger->log($this->type, new \MojDashButton\Models\DashButton(), 'Another Log');

        $logs = $this->db->fetchAll('SELECT * FROM moj_dash_log WHERE ' . $this->whereType);
        $this->assertCount(5, $logs);

        return $logger;
    }

    /**
     * @depends testLog
     * @param $logger
     */
    public function testLogAndCollect(\MojDashButton\Services\Core\Logger $logger)
    {
        $button = $this->createButton($this->getButtonCode());

        $logger->log($this->type, null, 'Another Log with Buttoncode: ' . $button->getButtonCode());
        $logger->log($this->type, $button, 'Another Log');
        $logger->log($this->type, null, $button->getButtonCode() . ': Another Log');

        $logs = $logger->collectLog($button);
        $this->assertCount(3, $logs);

        $this->removeButtons([$button]);
    }

}
